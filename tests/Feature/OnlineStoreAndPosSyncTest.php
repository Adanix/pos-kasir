<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\OmniKasirDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnlineStoreAndPosSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(OmniKasirDemoSeeder::class);
    }

    public function test_storefront_catalog_and_product_detail(): void
    {
        $response = $this->get(route('store.catalog'));
        $response->assertStatus(200);
        $response->assertSee('OmniKasir Store');

        $product = Product::where('is_featured_online', true)->first();
        $detail = $this->get(route('store.product.show', $product->slug));
        $detail->assertStatus(200);
        $detail->assertSee($product->name);
    }

    public function test_full_storefront_order_flow_with_qris_simulation_and_pos_sync(): void
    {
        $product = Product::where('stock_qty', '>=', 5)->where('is_featured_online', true)->first();
        $stockBefore = $product->stock_qty;

        // 1. Add to cart
        $this->post(route('store.cart.add'), [
            'product_id' => $product->id,
            'qty' => 2,
        ])->assertSessionHas('success');

        // Check cart page
        $cartPage = $this->get(route('store.cart.index'));
        $cartPage->assertStatus(200);
        $cartPage->assertSee($product->name);

        // 2. Checkout
        $checkoutPayload = [
            'customer_name' => 'Dewi Lestari',
            'customer_phone' => '081377889900',
            'customer_email' => 'dewi@example.com',
            'fulfillment_type' => 'delivery',
            'shipping_courier' => 'jne_reg',
            'customer_address' => 'Jl. Diponegoro No. 10, Semarang',
            'customer_notes' => 'Tolong kirim sebelum jam 3 sore',
            'payment_method' => 'qris',
        ];

        $checkoutResponse = $this->post(route('store.checkout.store'), $checkoutPayload);

        $order = Order::where('customer_name', 'Dewi Lestari')->latest('id')->firstOrFail();
        $checkoutResponse->assertRedirect(route('store.payment.show', $order->order_number));

        // Stock was reserved (decremented by 2)
        $this->assertEquals($stockBefore - 2, $product->fresh()->stock_qty);

        // 3. Payment simulation screen
        $paymentPage = $this->get(route('store.payment.show', $order->order_number));
        $paymentPage->assertStatus(200);
        $paymentPage->assertSee('QRIS');
        $paymentPage->assertSee('Simulasikan Berhasil Lunas');

        // 4. Trigger simulation outcome 'success'
        $simulateResponse = $this->post(route('store.payment.simulate', $order->order_number), [
            'outcome' => 'success',
        ]);
        $simulateResponse->assertRedirect(route('store.orders.show', $order->order_number));

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('processing', $order->status);

        // 5. Appears in Cashier's Online Orders Queue
        $cashier = User::where('role', 'cashier')->first();
        $this->actingAs($cashier);

        $queueResponse = $this->get(route('pos.online-orders.index'));
        $queueResponse->assertStatus(200);
        $queueResponse->assertSee($order->order_number);
        $queueResponse->assertSee('Dewi Lestari');

        // Cashier updates status to shipped (generates tracking number)
        $shipResponse = $this->post(route('pos.online-orders.status', $order), [
            'status' => 'shipped',
        ]);
        $shipResponse->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('shipped', $order->status);
        $this->assertNotEmpty($order->shipping_tracking_number);

        // 6. Customer lookup & tracking
        $lookupResponse = $this->get(route('store.orders.lookup', ['q' => '081377889900']));
        $lookupResponse->assertStatus(200);
        $lookupResponse->assertSee($order->order_number);

        $trackingResponse = $this->get(route('store.orders.show', $order->order_number));
        $trackingResponse->assertStatus(200);
        $trackingResponse->assertSee($order->shipping_tracking_number);
    }
}
