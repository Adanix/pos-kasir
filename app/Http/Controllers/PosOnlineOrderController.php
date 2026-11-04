<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OnlineOrderService;
use App\Services\Simulators\DemoShippingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PosOnlineOrderController extends Controller
{
    public function __construct(
        protected OnlineOrderService $onlineOrderService,
        protected DemoShippingService $shippingService
    ) {}

    public function index(Request $request): View
    {
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Order::with(['items.product', 'paymentTransactions'])
            ->where('channel', 'online')
            ->latest('ordered_at');

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        // Queue counters
        $counts = [
            'pending_payment' => Order::where('channel', 'online')->where('status', 'pending_payment')->count(),
            'paid_or_processing' => Order::where('channel', 'online')->whereIn('status', ['paid_confirmed', 'processing'])->count(),
            'shipped' => Order::where('channel', 'online')->where('status', 'shipped')->count(),
            'completed' => Order::where('channel', 'online')->where('status', 'completed')->count(),
        ];

        return view('pos.online_orders.index', compact('orders', 'status', 'search', 'counts'));
    }

    public function confirmPayment(Order $order): RedirectResponse
    {
        try {
            $this->onlineOrderService->confirmPayment($order, Auth::user());
            return back()->with('success', "Pembayaran untuk pesanan {$order->order_number} berhasil dikonfirmasi.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal konfirmasi pembayaran: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:processing,shipped,delivered,completed,cancelled'],
        ]);

        $newStatus = $validated['status'];

        if ($newStatus === 'shipped' && empty($order->shipping_tracking_number)) {
            $waybill = $this->shippingService->generateWaybill($order->shipping_courier ?? 'jne_reg');
            $order->shipping_tracking_number = $waybill;
        }

        if ($newStatus === 'completed') {
            $order->completed_at = now();
        }

        $order->status = $newStatus;
        $order->save();

        return back()->with('success', "Status pesanan {$order->order_number} diperbarui menjadi: " . strtoupper($newStatus));
    }
}
