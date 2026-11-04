<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreCartController extends Controller
{
    public function index(): View
    {
        $sessionCart = session()->get('cart', []);
        $cartItems = [];
        $subtotal = 0.0;
        $totalWeightGrams = 0;

        if (!empty($sessionCart)) {
            $productIds = array_keys($sessionCart);
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            foreach ($sessionCart as $pId => $item) {
                if (isset($products[$pId])) {
                    $prod = $products[$pId];
                    $qty = min($item['qty'], $prod->stock_qty > 0 ? $prod->stock_qty : 1);
                    $lineTotal = $prod->selling_price * $qty;

                    $cartItems[] = [
                        'product' => $prod,
                        'qty' => $qty,
                        'unit_price' => (float) $prod->selling_price,
                        'line_total' => $lineTotal,
                        'has_sufficient_stock' => ($prod->stock_qty >= $qty && $prod->stock_qty > 0),
                    ];

                    $subtotal += $lineTotal;
                    $totalWeightGrams += ($prod->weight_grams * $qty);
                }
            }
        }

        return view('store.cart', compact('cartItems', 'subtotal', 'totalWeightGrams'));
    }

    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        if ($product->stock_qty <= 0) {
            return back()->with('error', "Stok produk {$product->name} saat ini sedang kosong.");
        }

        $cart = session()->get('cart', []);
        $currentQty = isset($cart[$product->id]) ? $cart[$product->id]['qty'] : 0;
        $newQty = $currentQty + $validated['qty'];

        if ($newQty > $product->stock_qty) {
            $newQty = $product->stock_qty;
        }

        $cart[$product->id] = [
            'product_id' => $product->id,
            'qty' => $newQty,
        ];

        session()->put('cart', $cart);

        if ($request->has('buy_now')) {
            return redirect()->route('store.checkout.index');
        }

        return back()->with('success', "{$product->name} berhasil ditambahkan ke keranjang belanja.");
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $qty = min($validated['qty'], $product->stock_qty);
            $cart[$product->id]['qty'] = $qty;
            session()->put('cart', $cart);
        }

        return redirect()->route('store.cart.index')->with('success', 'Kuantitas keranjang berhasil diperbarui.');
    }

    public function remove(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $cart = session()->get('cart', []);
        if (isset($cart[$validated['product_id']])) {
            unset($cart[$validated['product_id']]);
            session()->put('cart', $cart);
        }

        return redirect()->route('store.cart.index')->with('success', 'Produk berhasil dihapus dari keranjang.');
    }
}
