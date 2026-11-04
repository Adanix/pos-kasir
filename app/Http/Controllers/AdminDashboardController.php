<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\CashbookEntry;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today()->toDateString();

        // Revenue Today
        $posRevenueToday = (float) Order::where('channel', 'pos')
            ->where('payment_status', 'paid')
            ->whereDate('ordered_at', $today)
            ->sum('grand_total');

        $onlineRevenueToday = (float) Order::where('channel', 'online')
            ->where('payment_status', 'paid')
            ->whereDate('ordered_at', $today)
            ->sum('grand_total');

        $totalRevenueToday = $posRevenueToday + $onlineRevenueToday;

        // Cashbook Current Balance
        $latestCashbook = CashbookEntry::orderBy('id', 'desc')->first();
        $currentCashBalance = $latestCashbook ? (float) $latestCashbook->balance_after : 0.0;

        // Pending Online Orders
        $pendingOnlineOrdersCount = Order::where('channel', 'online')
            ->whereIn('status', ['pending_payment', 'pending_confirmation', 'processing', 'ready_for_pickup'])
            ->count();

        // Low Stock Count
        $lowStockProducts = Product::with('category')
            ->whereColumn('stock_qty', '<=', 'min_stock_alert')
            ->where('is_active', true)
            ->take(5)
            ->get();
        $lowStockCount = Product::whereColumn('stock_qty', '<=', 'min_stock_alert')->where('is_active', true)->count();

        // Today's Attendances
        $todayAttendances = Attendance::with('user')
            ->whereDate('attendance_date', $today)
            ->get();

        // Recent Orders
        $recentOrders = Order::with('cashier')
            ->latest('ordered_at')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'posRevenueToday',
            'onlineRevenueToday',
            'totalRevenueToday',
            'currentCashBalance',
            'pendingOnlineOrdersCount',
            'lowStockProducts',
            'lowStockCount',
            'todayAttendances',
            'recentOrders'
        ));
    }
}
