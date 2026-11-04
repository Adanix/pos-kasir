<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarehouseInventoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $filterStock = $request->input('stock_status');

        $query = Product::with('category')->orderBy('name', 'asc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($filterStock === 'low') {
            $query->whereColumn('stock_qty', '<=', 'min_stock_alert')->where('stock_qty', '>', 0);
        } elseif ($filterStock === 'out') {
            $query->where('stock_qty', '<=', 0);
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        // Inventory KPIs
        $totalSkus = Product::count();
        $outOfStockCount = Product::where('stock_qty', '<=', 0)->count();
        $lowStockCount = Product::whereColumn('stock_qty', '<=', 'min_stock_alert')->where('stock_qty', '>', 0)->count();
        
        $allProducts = Product::all();
        $totalAssetValue = $allProducts->sum(fn ($p) => $p->stock_qty * $p->cost_price);
        $totalSellingValue = $allProducts->sum(fn ($p) => $p->stock_qty * $p->selling_price);

        return view('warehouse.inventory.index', compact(
            'products',
            'categories',
            'search',
            'categoryId',
            'filterStock',
            'totalSkus',
            'outOfStockCount',
            'lowStockCount',
            'totalAssetValue',
            'totalSellingValue'
        ));
    }
}
