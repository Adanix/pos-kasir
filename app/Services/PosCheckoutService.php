<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\PosShift;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosCheckoutService
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected CashbookService $cashbookService
    ) {}

    /**
     * Process walk-in in-store POS sale transaction.
     */
    public function processWalkInOrder(array $validatedPayload, User $cashier): Order
    {
        return DB::transaction(function () use ($validatedPayload, $cashier) {
            $items = $validatedPayload['items']; // array of ['product_id', 'qty', 'discount']
            $paymentMethod = $validatedPayload['payment_method'] ?? 'cash';
            $discountOrder = (float) ($validatedPayload['discount_order'] ?? 0);
            $applyTax = (bool) ($validatedPayload['apply_tax'] ?? false);
            $cashReceived = (float) ($validatedPayload['cash_received'] ?? 0);
            $customerName = trim($validatedPayload['customer_name'] ?? 'Pelanggan Umum');
            if ($customerName === '') {
                $customerName = 'Pelanggan Umum';
            }

            // Find or associate active shift
            $activeShift = PosShift::where('user_id', $cashier->id)
                ->where('status', 'open')
                ->latest()
                ->first();

            $orderNumber = 'ORD-POS-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            $clientTxId = $validatedPayload['client_tx_id'] ?? null;

            // Idempotency check if client_tx_id provided
            if ($clientTxId) {
                $existing = Order::where('client_tx_id', $clientTxId)->first();
                if ($existing) {
                    return $existing;
                }
            }

            // Calculate items & deduct stock
            $subtotal = 0.0;
            $totalItemDiscounts = 0.0;
            $orderItemsData = [];

            foreach ($items as $itemData) {
                $productId = (int) $itemData['product_id'];
                $qty = (int) $itemData['qty'];
                $itemDiscount = (float) ($itemData['discount'] ?? 0);

                if ($qty <= 0) {
                    continue;
                }

                $product = Product::whereKey($productId)->lockForUpdate()->firstOrFail();

                if ($product->stock_qty < $qty) {
                    throw new \InvalidArgumentException("Stok tidak mencukupi untuk '{$product->name}'. Tersedia: {$product->stock_qty}, diminta: {$qty}.");
                }

                $unitPrice = (float) $product->selling_price;
                $lineTotal = max(0, ($unitPrice * $qty) - $itemDiscount);

                $subtotal += ($unitPrice * $qty);
                $totalItemDiscounts += $itemDiscount;

                $orderItemsData[] = [
                    'product' => $product,
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'discount_amount' => $itemDiscount,
                    'line_total' => $lineTotal,
                ];
            }

            if (empty($orderItemsData)) {
                throw new \InvalidArgumentException("Keranjang belanja kasir kosong.");
            }

            $totalDiscount = $totalItemDiscounts + $discountOrder;
            $taxableAmount = max(0, $subtotal - $totalDiscount);
            $taxAmount = $applyTax ? ($taxableAmount * 0.11) : 0.0;
            $grandTotal = $taxableAmount + $taxAmount;

            if ($paymentMethod === 'cash') {
                if ($cashReceived < $grandTotal) {
                    throw new \InvalidArgumentException("Uang tunai diterima (Rp " . number_format($cashReceived, 0, ',', '.') . ") kurang dari total tagihan (Rp " . number_format($grandTotal, 0, ',', '.') . ").");
                }
                $changeAmount = $cashReceived - $grandTotal;
            } else {
                $cashReceived = $grandTotal;
                $changeAmount = 0.0;
            }

            // Create Order
            $order = Order::create([
                'order_number' => $orderNumber,
                'client_tx_id' => $clientTxId,
                'channel' => 'pos',
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => $paymentMethod,
                'cashier_id' => $cashier->id,
                'pos_shift_id' => $activeShift?->id,
                'customer_name' => $customerName,
                'customer_phone' => $validatedPayload['customer_phone'] ?? null,
                'fulfillment_type' => 'walk_in',
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount,
                'tax_amount' => $taxAmount,
                'shipping_cost' => 0,
                'grand_total' => $grandTotal,
                'cash_received' => $cashReceived,
                'change_amount' => $changeAmount,
                'print_count' => 0,
                'is_offline_synced' => !empty($clientTxId),
                'ordered_at' => now(),
                'completed_at' => now(),
            ]);

            // Create Order Items and execute stock movements
            foreach ($orderItemsData as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_name_snapshot' => $item['product']->name,
                    'sku_snapshot' => $item['product']->sku,
                    'unit_snapshot' => $item['product']->unit,
                    'cost_price_snapshot' => (float) $item['product']->cost_price,
                    'unit_price' => $item['unit_price'],
                    'qty' => $item['qty'],
                    'discount_amount' => $item['discount_amount'],
                    'line_total' => $item['line_total'],
                ]);

                $this->inventoryService->recordMovement(
                    product: $item['product'],
                    qtyChange: -$item['qty'],
                    movementType: 'pos_sale',
                    referenceType: 'order',
                    referenceId: $order->id,
                    notes: "Penjualan Kasir POS #{$order->order_number}",
                    userId: $cashier->id
                );
            }

            // Create Payment Transaction
            PaymentTransaction::create([
                'order_id' => $order->id,
                'reference_number' => 'PAY-POS-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                'payment_method' => $paymentMethod,
                'amount' => $grandTotal,
                'status' => 'paid',
                'paid_at' => now(),
                'processed_by' => $cashier->id,
            ]);

            // Update Shift cash counts
            if ($activeShift) {
                if ($paymentMethod === 'cash') {
                    $activeShift->increment('cash_sales_total', $grandTotal);
                } else {
                    $activeShift->increment('non_cash_sales_total', $grandTotal);
                }
                $activeShift->closing_cash_expected = $activeShift->opening_cash + $activeShift->cash_sales_total - $activeShift->petty_cash_out_total;
                $activeShift->save();
            }

            // Record into Cashbook
            $this->cashbookService->recordEntry(
                type: 'in',
                category: 'penjualan_pos',
                amount: $grandTotal,
                description: "Penjualan Kasir POS #{$order->order_number} ({$customerName})",
                paymentMethod: $paymentMethod,
                referenceNumber: $order->order_number,
                orderId: $order->id,
                posShiftId: $activeShift?->id,
                userId: $cashier->id
            );

            return $order->load(['items.product', 'cashier']);
        });
    }

    /**
     * Batch synchronize queued offline orders from IndexedDB.
     */
    public function syncOfflineOrders(array $queuedOrders, User $cashier): array
    {
        $synced = [];
        $errors = [];

        foreach ($queuedOrders as $index => $offlineTx) {
            try {
                $order = $this->processWalkInOrder($offlineTx, $cashier);
                $synced[] = [
                    'client_tx_id' => $offlineTx['client_tx_id'] ?? null,
                    'order_number' => $order->order_number,
                    'grand_total' => (float) $order->grand_total,
                ];
            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'client_tx_id' => $offlineTx['client_tx_id'] ?? null,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'synced_count' => count($synced),
            'synced_orders' => $synced,
            'errors' => $errors,
        ];
    }
}
