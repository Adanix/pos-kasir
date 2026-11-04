<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\User;
use App\Services\Simulators\DemoNotificationService;
use App\Services\Simulators\DemoPaymentGatewayService;
use App\Services\Simulators\DemoShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OnlineOrderService
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected CashbookService $cashbookService,
        protected DemoShippingService $shippingService,
        protected DemoNotificationService $notificationService,
        protected DemoPaymentGatewayService $paymentGatewayService
    ) {}

    /**
     * Create new online order from public storefront checkout with stock reservation.
     */
    public function createFromStorefront(array $cartItems, array $customerData): Order
    {
        return DB::transaction(function () use ($cartItems, $customerData) {
            $fulfillmentType = $customerData['fulfillment_type'] ?? 'delivery';
            $courierCode = $customerData['shipping_courier'] ?? 'jne_reg';
            $paymentMethod = $customerData['payment_method'] ?? 'qris';

            $orderNumber = 'ORD-WEB-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            $subtotal = 0.0;
            $totalWeight = 0;
            $orderItemsData = [];

            foreach ($cartItems as $item) {
                $productId = (int) $item['product_id'];
                $qty = (int) $item['qty'];

                if ($qty <= 0) {
                    continue;
                }

                $product = Product::whereKey($productId)->lockForUpdate()->firstOrFail();

                if ($product->stock_qty < $qty) {
                    throw new \InvalidArgumentException("Stok tidak mencukupi untuk '{$product->name}'. Tersedia: {$product->stock_qty}, diminta: {$qty}.");
                }

                $unitPrice = (float) $product->selling_price;
                $lineTotal = $unitPrice * $qty;

                $subtotal += $lineTotal;
                $totalWeight += ($product->weight_grams * $qty);

                $orderItemsData[] = [
                    'product' => $product,
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            }

            if (empty($orderItemsData)) {
                throw new \InvalidArgumentException('Keranjang belanja kosong.');
            }

            // Calculate shipping cost
            $shippingCost = 0.0;
            $shippingServiceName = null;

            if ($fulfillmentType === 'delivery') {
                $shippingCost = $this->shippingService->calculateCost($courierCode, $totalWeight);
                $options = $this->shippingService->getAvailableOptions($totalWeight);
                foreach ($options as $opt) {
                    if ($opt['code'] === $courierCode) {
                        $shippingServiceName = "{$opt['name']} ({$opt['service']})";
                        break;
                    }
                }
            } else {
                $courierCode = 'pickup';
                $shippingServiceName = 'Ambil di Toko';
            }

            $grandTotal = $subtotal + $shippingCost;

            // Initial status
            $status = ($paymentMethod === 'cod') ? 'pending_confirmation' : 'pending_payment';
            $paymentStatus = 'unpaid';

            $order = Order::create([
                'order_number' => $orderNumber,
                'channel' => 'online',
                'status' => $status,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
                'customer_name' => $customerData['customer_name'],
                'customer_phone' => $customerData['customer_phone'],
                'customer_email' => $customerData['customer_email'] ?? null,
                'fulfillment_type' => $fulfillmentType,
                'shipping_courier' => $courierCode,
                'shipping_service' => $shippingServiceName,
                'customer_address' => $customerData['customer_address'] ?? null,
                'customer_notes' => $customerData['customer_notes'] ?? null,
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'shipping_cost' => $shippingCost,
                'grand_total' => $grandTotal,
                'cash_received' => 0,
                'change_amount' => 0,
                'print_count' => 0,
                'ordered_at' => now(),
            ]);

            // Create items and reserve stock
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
                    'discount_amount' => 0,
                    'line_total' => $item['line_total'],
                ]);

                // Atomically reserve stock
                $this->inventoryService->recordMovement(
                    product: $item['product'],
                    qtyChange: -$item['qty'],
                    movementType: 'online_reserve',
                    referenceType: 'order',
                    referenceId: $order->id,
                    notes: "Reservasi Stok Pesanan Online #{$order->order_number}"
                );
            }

            // Create Payment Transaction Record
            $ref = 'PAY-' . strtoupper($paymentMethod) . '-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            $paymentData = [
                'order_id' => $order->id,
                'reference_number' => $ref,
                'payment_method' => $paymentMethod,
                'amount' => $grandTotal,
                'status' => 'pending',
            ];

            if ($paymentMethod === 'qris') {
                $qris = $this->paymentGatewayService->generateQrisPayload($order);
                $paymentData['simulator_qr_string'] = $qris['qr_string'];
            } elseif ($paymentMethod === 'bank_transfer') {
                $va = $this->paymentGatewayService->generateVaPayload($order);
                $paymentData['simulator_va_number'] = $va['va_number'];
            }

            PaymentTransaction::create($paymentData);

            // Log notification
            $this->notificationService->sendOrderCreatedNotification($order);

            return $order->load('items.product');
        });
    }

    /**
     * Cashier confirms online payment (e.g. COD or verified transfer).
     */
    public function confirmPayment(Order $order, ?User $actor = null): Order
    {
        return DB::transaction(function () use ($order, $actor) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($lockedOrder->isPaid()) {
                return $lockedOrder;
            }

            $lockedOrder->payment_status = 'paid';
            if ($lockedOrder->status === 'pending_payment' || $lockedOrder->status === 'pending_confirmation') {
                $lockedOrder->status = 'processing';
            }
            $lockedOrder->save();

            // Update or create payment transaction
            $payment = $lockedOrder->latestPayment;
            if ($payment) {
                $payment->status = 'paid';
                $payment->paid_at = now();
                $payment->processed_by = $actor?->id;
                $payment->save();
            }

            // Record into Cashbook
            $this->cashbookService->recordEntry(
                type: 'in',
                category: 'penjualan_online',
                amount: (float) $lockedOrder->grand_total,
                description: "Konfirmasi pembayaran pesanan online #{$lockedOrder->order_number} ({$lockedOrder->customer_name})",
                paymentMethod: $lockedOrder->payment_method,
                referenceNumber: $lockedOrder->order_number,
                orderId: $lockedOrder->id,
                userId: $actor?->id
            );

            $this->notificationService->sendPaymentReceiptNotification($lockedOrder);

            return $lockedOrder;
        });
    }

    /**
     * Update fulfillment status (processing -> ready_for_pickup/shipped -> completed).
     */
    public function updateFulfillmentStatus(Order $order, string $newStatus, ?string $trackingNumber = null, ?User $actor = null): Order
    {
        return DB::transaction(function () use ($order, $newStatus, $trackingNumber, $actor) {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($newStatus === 'cancelled') {
                // If cancelled, release reserved stock back
                $locked->status = 'cancelled';
                $locked->payment_status = 'cancelled';
                $locked->save();

                foreach ($locked->items as $item) {
                    if ($item->product) {
                        $this->inventoryService->recordMovement(
                            product: $item->product,
                            qtyChange: $item->qty,
                            movementType: 'online_release',
                            referenceType: 'order',
                            referenceId: $locked->id,
                            notes: "Pembatalan pesanan online #{$locked->order_number}",
                            userId: $actor?->id
                        );
                    }
                }
                return $locked;
            }

            $locked->status = $newStatus;

            if ($newStatus === 'shipped') {
                $locked->tracking_number = $trackingNumber ?: $this->shippingService->generateTrackingNumber($locked->shipping_courier ?: 'jne');
            } elseif ($newStatus === 'completed') {
                $locked->completed_at = now();
            }

            $locked->save();
            return $locked;
        });
    }
}
