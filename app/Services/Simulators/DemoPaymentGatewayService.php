<?php

namespace App\Services\Simulators;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\CashbookService;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoPaymentGatewayService
{
    public function __construct(
        protected CashbookService $cashbookService,
        protected InventoryService $inventoryService,
        protected DemoNotificationService $notificationService
    ) {}

    /**
     * Generate dynamic QRIS payload and SVG representation.
     */
    public function generateQrisPayload(Order $order): array
    {
        $ref = 'PAY-QRIS-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        $nominal = (int) $order->grand_total;

        // Simulated EMVCo QRIS string
        $qrisString = "00020101021226600016ID.CO.OMNIKASIR.WWW01189360099900000000010215OMNIKASIRNUSANTARA520454115303360540" .
            strlen((string) $nominal) . $nominal . "5802ID5919OMNIKASIR NUSANTARA6007SEMARANG62240120" . $ref . "6304ABCD";

        return [
            'reference_number' => $ref,
            'qr_string' => $qrisString,
            'merchant_name' => 'OMNIKASIR NUSANTARA',
            'amount' => $nominal,
        ];
    }

    /**
     * Generate Virtual Account payload.
     */
    public function generateVaPayload(Order $order): array
    {
        $ref = 'PAY-VA-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        $bank = 'BCA';
        $vaNumber = '88012' . str_pad((string) $order->id, 8, '0', STR_PAD_LEFT);

        return [
            'reference_number' => $ref,
            'bank' => $bank,
            'va_number' => $vaNumber,
            'amount' => (float) $order->grand_total,
        ];
    }

    /**
     * Process simulated outcome (success, failed, cancelled) idempotently.
     */
    public function processSimulatedOutcome(PaymentTransaction $payment, string $outcome, ?User $actor = null): PaymentTransaction
    {
        return DB::transaction(function () use ($payment, $outcome, $actor) {
            $lockedPayment = PaymentTransaction::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            $order = Order::whereKey($lockedPayment->order_id)->lockForUpdate()->firstOrFail();

            // Idempotency: if already paid, do not double-process
            if ($lockedPayment->status === 'paid' && $outcome === 'success') {
                return $lockedPayment;
            }

            if ($outcome === 'success') {
                $lockedPayment->status = 'paid';
                $lockedPayment->paid_at = now();
                $lockedPayment->processed_by = $actor?->id;
                $lockedPayment->save();

                $order->payment_status = 'paid';
                if ($order->status === 'pending_payment' || $order->status === 'pending_confirmation') {
                    $order->status = 'processing';
                }
                $order->save();

                // Record into Cashbook
                $category = $order->isOnline() ? 'penjualan_online' : 'penjualan_pos';
                $this->cashbookService->recordEntry(
                    type: 'in',
                    category: $category,
                    amount: (float) $order->grand_total,
                    description: "Pembayaran lunas pesanan #{$order->order_number} via {$lockedPayment->payment_method}",
                    paymentMethod: $lockedPayment->payment_method,
                    referenceNumber: $order->order_number,
                    orderId: $order->id,
                    posShiftId: $order->pos_shift_id,
                    userId: $actor?->id
                );

                // Send simulated notification
                $this->notificationService->sendPaymentReceiptNotification($order);

            } elseif ($outcome === 'failed') {
                $lockedPayment->status = 'failed';
                $lockedPayment->save();

                // Order payment status remains unpaid so user can retry

            } elseif ($outcome === 'cancelled' || $outcome === 'expired') {
                $lockedPayment->status = 'cancelled';
                $lockedPayment->save();

                $order->payment_status = 'cancelled';
                $order->status = 'cancelled';
                $order->save();

                // Restore reserved stock for all items
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $this->inventoryService->recordMovement(
                            product: $item->product,
                            qtyChange: $item->qty, // positive to restore
                            movementType: 'online_release',
                            referenceType: 'order',
                            referenceId: $order->id,
                            notes: "Pengembalian stok pembatalan pesanan #{$order->order_number}",
                            userId: $actor?->id
                        );
                    }
                }
            }

            return $lockedPayment;
        });
    }
}
