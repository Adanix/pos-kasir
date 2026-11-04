<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Simulators\DemoPaymentGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StorePaymentController extends Controller
{
    public function __construct(
        protected DemoPaymentGatewayService $paymentGatewayService
    ) {}

    public function show(string $orderNumber): View|RedirectResponse
    {
        $order = Order::with(['items.product', 'paymentTransactions'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        if ($order->isPaid()) {
            return redirect()->route('store.orders.show', $order->order_number);
        }

        $payment = $order->latestPayment;
        $qrisPayload = null;
        $vaPayload = null;

        if ($order->payment_method === 'qris') {
            $qrisPayload = $this->paymentGatewayService->generateQrisPayload($order);
        } elseif ($order->payment_method === 'bank_transfer') {
            $vaPayload = $this->paymentGatewayService->generateVaPayload($order);
        }

        return view('store.payment', compact('order', 'payment', 'qrisPayload', 'vaPayload'));
    }

    public function simulate(Request $request, string $orderNumber): RedirectResponse
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $validated = $request->validate([
            'outcome' => ['required', 'in:success,failed,cancelled'],
        ]);

        $payment = $order->latestPayment;
        if (!$payment) {
            return back()->with('error', 'Transaksi pembayaran tidak ditemukan.');
        }

        $this->paymentGatewayService->processSimulatedOutcome($payment, $validated['outcome'], Auth::user());

        if ($validated['outcome'] === 'success') {
            return redirect()->route('store.orders.show', $order->order_number)
                ->with('success', 'Pembayaran QRIS / VA berhasil disimulasikan! Pesanan Anda kini sedang diproses.');
        } elseif ($validated['outcome'] === 'failed') {
            return back()->with('error', 'Simulasi pembayaran GAGAL (Transaksi ditolak / Saldo tidak cukup). Anda dapat mencoba kembali.');
        } else {
            return redirect()->route('store.orders.show', $order->order_number)
                ->with('info', 'Pesanan telah dibatalkan. Stok barang otomatis dikembalikan ke inventori toko.');
        }
    }
}
