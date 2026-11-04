<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreOrderController extends Controller
{
    public function lookup(Request $request): View
    {
        $query = $request->input('q');
        $orders = collect();

        if ($query) {
            $orders = Order::with('items')
                ->where('order_number', 'like', "%{$query}%")
                ->orWhere('customer_phone', 'like', "%{$query}%")
                ->latest('ordered_at')
                ->take(10)
                ->get();
        }

        return view('store.orders.lookup', compact('orders', 'query'));
    }

    public function show(string $orderNumber): View
    {
        $order = Order::with(['items.product', 'paymentTransactions'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('store.orders.show', compact('order'));
    }
}
