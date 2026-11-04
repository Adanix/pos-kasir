<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PosShift;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\OmniKasirDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosTransactionAndOfflineSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(OmniKasirDemoSeeder::class);
    }

    public function test_catalog_snapshot_json_endpoint(): void
    {
        $cashier = User::where('role', 'cashier')->first();
        $this->actingAs($cashier);

        $response = $this->getJson(route('pos.catalog.snapshot'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'snapshot_at',
            'categories',
            'products',
        ]);
    }

    public function test_pos_walk_in_checkout_success(): void
    {
        $cashier = User::where('role', 'cashier')->first();
        $this->actingAs($cashier);

        $product = Product::where('stock_qty', '>=', 5)->first();
        $stockBefore = $product->stock_qty;
        $grandTotal = ($product->selling_price * 2) - 5000 + ((($product->selling_price * 2) - 5000) * 0.11);
        $cashReceived = ceil($grandTotal / 50000) * 50000 + 50000;

        $payload = [
            'customer_name' => 'Pelanggan Meja 1',
            'customer_phone' => '081299998888',
            'payment_method' => 'cash',
            'discount_order' => 5000,
            'apply_tax' => true,
            'cash_received' => $cashReceived,
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 2,
                    'discount' => 0,
                ],
            ],
        ];

        $response = $this->postJson(route('pos.checkout'), $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $orderId = $response->json('order.id');
        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'customer_name' => 'Pelanggan Meja 1',
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        // Stock must be decremented by 2
        $this->assertEquals($stockBefore - 2, $product->fresh()->stock_qty);

        // Receipt can be rendered
        $receiptResponse = $this->get(route('pos.orders.receipt', ['order' => $orderId, 'width' => '58']));
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee($response->json('order.order_number'));

        // ESC/POS Bridge simulation
        $bridgeResponse = $this->postJson(route('pos.orders.print-bridge', ['order' => $orderId]), [
            'paper_size' => '58mm',
        ]);
        $bridgeResponse->assertStatus(200);
        $bridgeResponse->assertJson(['status' => 'success']);
    }

    public function test_pos_offline_batch_sync(): void
    {
        $cashier = User::where('role', 'cashier')->first();
        $this->actingAs($cashier);

        $product = Product::where('stock_qty', '>=', 5)->first();
        $stockBefore = $product->stock_qty;
        $total = (float) $product->selling_price;
        $cashReceived = ceil($total / 50000) * 50000 + 50000;

        $clientTxId = 'TX-OFFLINE-TEST-' . uniqid();
        $payload = [
            'transactions' => [
                [
                    'client_tx_id' => $clientTxId,
                    'customer_name' => 'Pelanggan Offline Mode',
                    'payment_method' => 'cash',
                    'discount_order' => 0,
                    'apply_tax' => false,
                    'cash_received' => $cashReceived,
                    'items' => [
                        [
                            'product_id' => $product->id,
                            'qty' => 1,
                            'discount' => 0,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->postJson(route('pos.sync.offline'), $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'result' => [
                'synced_count' => 1,
            ],
        ]);

        $this->assertDatabaseHas('orders', [
            'client_tx_id' => $clientTxId,
            'customer_name' => 'Pelanggan Offline Mode',
            'is_offline_synced' => true,
        ]);

        $this->assertEquals($stockBefore - 1, $product->fresh()->stock_qty);

        // Idempotency test: syncing the same transaction ID twice does not double-decrement
        $responseSecond = $this->postJson(route('pos.sync.offline'), $payload);
        $responseSecond->assertStatus(200);
        $this->assertEquals($stockBefore - 1, $product->fresh()->stock_qty);
    }

    public function test_shift_management_and_petty_expense(): void
    {
        $cashier = User::where('role', 'cashier')->first();
        $this->actingAs($cashier);

        // Close any active shift first if seeded
        $existing = PosShift::where('user_id', $cashier->id)->where('status', 'open')->first();
        if ($existing) {
            $this->post(route('pos.shift.close'), [
                'closing_cash_actual' => $existing->closing_cash_expected,
                'notes' => 'Tutup shift lama untuk pengujian',
            ]);
        }

        // Open shift
        $openResponse = $this->post(route('pos.shift.open'), [
            'shift_name' => 'Pagi',
            'opening_cash' => 250000,
            'notes' => 'Buka kasir pagi',
        ]);
        $openResponse->assertRedirect(route('pos.index'));

        $shift = PosShift::where('user_id', $cashier->id)->where('status', 'open')->firstOrFail();
        $this->assertEquals(250000, $shift->opening_cash);

        // Record petty cash expense
        $expenseResponse = $this->post(route('pos.shift.expense'), [
            'amount' => 15000,
            'description' => 'Beli kantong plastik kresek',
        ]);
        $expenseResponse->assertSessionHas('success');

        $shift->refresh();
        $this->assertEquals(15000, $shift->petty_cash_out_total);
        $this->assertEquals(235000, $shift->closing_cash_expected);

        // Close shift
        $closeResponse = $this->post(route('pos.shift.close'), [
            'closing_cash_actual' => 235000,
            'notes' => 'Kas cocok pas',
        ]);
        $closeResponse->assertRedirect(route('pos.shift.index'));

        $shift->refresh();
        $this->assertEquals('closed', $shift->status);
        $this->assertEquals(0, $shift->difference_amount);
    }
}
