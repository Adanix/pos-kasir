<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $categorySlug = $request->input('category');
        $sort = $request->input('sort', 'newest');

        $query = Product::with('category')
            ->where('is_active', true)
            ->where('is_featured_online', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($categorySlug) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        match ($sort) {
            'price_low' => $query->orderBy('selling_price', 'asc'),
            'price_high' => $query->orderBy('selling_price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            default => $query->orderBy('id', 'desc'),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)->where('is_featured_online', true)])
            ->orderBy('name')
            ->get();

        return view('store.catalog', compact('products', 'categories', 'search', 'categorySlug', 'sort'));
    }

    public function show(string $slug): View
    {
        $product = Product::with('category')
            ->where('is_active', true)
            ->where(fn ($q) => $q->where('slug', $slug)->orWhere('id', $slug))
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('is_featured_online', true)
            ->take(4)
            ->get();

        return view('store.product', compact('product', 'relatedProducts'));
    }
}
