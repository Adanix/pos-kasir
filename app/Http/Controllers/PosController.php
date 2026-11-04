<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\PosShift;
use App\Models\Product;
use App\Services\PosCheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Get or check active shift
        $activeShift = PosShift::where('user_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        $categories = Category::where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        $products = Product::where('is_active', true)
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('pos.index', compact('activeShift', 'categories', 'products'));
    }

    public function catalogSnapshot(): JsonResponse
    {
        $categories = Category::where('is_active', true)->select('id', 'name', 'slug')->get();
        $products = Product::where('is_active', true)
            ->select('id', 'category_id', 'sku', 'barcode', 'name', 'unit', 'selling_price', 'stock_qty', 'image_url')
            ->get();

        return response()->json([
            'snapshot_at' => now()->toIso8601String(),
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    public function checkout(Request $request, PosCheckoutService $posCheckoutService): JsonResponse
    {
        $validated = $request->validate([
            'client_tx_id' => ['nullable', 'string', 'max:100'],
            'customer_name' => ['nullable', 'string', 'max:100'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'payment_method' => ['required', 'string', 'in:cash,qris,bank_transfer,debit_card'],
            'discount_order' => ['nullable', 'numeric', 'min:0'],
            'apply_tax' => ['nullable', 'boolean'],
            'cash_received' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $order = $posCheckoutService->processWalkInOrder($validated, Auth::user());

            return response()->json([
                'success' => true,
                'message' => 'Transaksi kasir berhasil diselesaikan.',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'grand_total' => (float) $order->grand_total,
                    'cash_received' => (float) $order->cash_received,
                    'change_amount' => (float) $order->change_amount,
                    'receipt_url' => route('pos.orders.receipt', $order),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function syncOffline(Request $request, PosCheckoutService $posCheckoutService): JsonResponse
    {
        $validated = $request->validate([
            'transactions' => ['required', 'array'],
        ]);

        $result = $posCheckoutService->syncOfflineOrders($validated['transactions'], Auth::user());

        return response()->json([
            'success' => true,
            'message' => "{$result['synced_count']} transaksi offline berhasil disinkronkan ke server.",
            'result' => $result,
        ]);
    }

    public function transactions(Request $request): View
    {
        $search = $request->input('search');
        $date = $request->input('date', date('Y-m-d'));

        $query = Order::with(['items', 'cashier'])
            ->where('channel', 'pos')
            ->whereDate('ordered_at', $date)
            ->latest('ordered_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(20)->withQueryString();
        $totalSales = Order::where('channel', 'pos')->whereDate('ordered_at', $date)->where('status', 'completed')->sum('grand_total');
        $totalOrders = Order::where('channel', 'pos')->whereDate('ordered_at', $date)->where('status', 'completed')->count();

        return view('pos.transactions', compact('orders', 'date', 'search', 'totalSales', 'totalOrders'));
    }
}
