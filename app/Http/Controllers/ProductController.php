<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $query = Product::with('category')->orderBy('id', 'desc');

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

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('warehouse.products.index', compact('products', 'categories', 'search', 'categoryId'));
    }

    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('warehouse.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:50', 'unique:products'],
            'barcode' => ['required', 'string', 'max:50', 'unique:products'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:20'],
            'weight_grams' => ['required', 'integer', 'min:1'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'is_online_listed' => ['boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        $validated['stock_qty'] = $validated['stock'];
        $validated['min_stock_alert'] = $validated['min_stock'];
        $validated['is_featured_online'] = $request->boolean('is_online_listed', true);
        $validated['is_active'] = $request->boolean('is_active', true);
        unset($validated['stock'], $validated['min_stock'], $validated['is_online_listed']);

        $product = Product::create($validated);

        return redirect()->route('warehouse.products.index')->with('success', "Produk {$product->name} berhasil ditambahkan ke inventori.");
    }

    public function edit(Product $product): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('warehouse.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:50', Rule::unique('products')->ignore($product->id)],
            'barcode' => ['required', 'string', 'max:50', Rule::unique('products')->ignore($product->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:20'],
            'weight_grams' => ['required', 'integer', 'min:1'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'is_online_listed' => ['boolean'],
        ]);

        $validated['min_stock_alert'] = $validated['min_stock'];
        $validated['is_featured_online'] = $request->boolean('is_online_listed');
        $validated['is_active'] = $request->boolean('is_active');
        unset($validated['min_stock'], $validated['is_online_listed']);

        $product->update($validated);

        return redirect()->route('warehouse.products.index')->with('success', "Produk {$product->name} berhasil diperbarui.");
    }
}
