<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        // Base query for paid/completed orders in range
        $ordersQuery = Order::where('payment_status', 'paid')
            ->whereBetween('ordered_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);

        // Revenue by channel
        $posRevenue = (float) (clone $ordersQuery)->where('channel', 'pos')->sum('grand_total');
        $posCount = (clone $ordersQuery)->where('channel', 'pos')->count();

        $onlineRevenue = (float) (clone $ordersQuery)->where('channel', 'online')->sum('grand_total');
        $onlineCount = (clone $ordersQuery)->where('channel', 'online')->count();

        $totalRevenue = $posRevenue + $onlineRevenue;
        $totalOrdersCount = $posCount + $onlineCount;

        // Total Cost (HPP) from order items
        $orderIds = (clone $ordersQuery)->pluck('id');
        $totalHpp = (float) OrderItem::whereIn('order_id', $orderIds)
            ->select(DB::raw('SUM(cost_price_snapshot * qty) as total_cogs'))
            ->value('total_cogs');

        $grossProfit = $totalRevenue - $totalHpp;
        $profitMargin = ($totalRevenue > 0) ? round(($grossProfit / $totalRevenue) * 100, 1) : 0;

        // Top 5 Selling Products
        $topProducts = OrderItem::whereIn('order_id', $orderIds)
            ->select(
                'product_id',
                'product_name_snapshot',
                'sku_snapshot',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(line_total) as total_sales'),
                DB::raw('SUM(cost_price_snapshot * qty) as total_cost')
            )
            ->groupBy('product_id', 'product_name_snapshot', 'sku_snapshot')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        return view('admin.reports.index', compact(
            'startDate',
            'endDate',
            'posRevenue',
            'posCount',
            'onlineRevenue',
            'onlineCount',
            'totalRevenue',
            'totalOrdersCount',
            'totalHpp',
            'grossProfit',
            'profitMargin',
            'topProducts'
        ));
    }
}
