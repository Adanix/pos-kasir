<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    /**
     * Render the public landing page for OmniKasir.
     */
    public function home(): View
    {
        $stats = [
            'active_products' => Product::where('is_active', true)->count(),
            'categories_count' => Category::where('is_active', true)->count(),
            'total_orders' => Order::count(),
            'sync_accuracy' => '100%',
        ];

        $featuredProducts = Product::with('category')
            ->where('is_active', true)
            ->where('is_featured_online', true)
            ->take(6)
            ->get();

        return view('public.home', compact('stats', 'featuredProducts'));
    }

    /**
     * Render the public product documentation and guide page.
     */
    public function docs(): View
    {
        return view('public.docs');
    }
}
