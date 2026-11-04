<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\OnlineOrderService;
use App\Services\Simulators\DemoShippingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreCheckoutController extends Controller
{
    public function __construct(
        protected OnlineOrderService $onlineOrderService,
        protected DemoShippingService $shippingService
    ) {}

    public function index(): View|RedirectResponse
    {
        $sessionCart = session()->get('cart', []);
        if (empty($sessionCart)) {
            return redirect()->route('store.cart.index')->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        $productIds = array_keys($sessionCart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $cartItems = [];
        $subtotal = 0.0;
        $totalWeightGrams = 0;

        foreach ($sessionCart as $pId => $item) {
            if (isset($products[$pId])) {
                $prod = $products[$pId];
                $qty = min($item['qty'], $prod->stock_qty > 0 ? $prod->stock_qty : 1);
                $lineTotal = $prod->selling_price * $qty;

                $cartItems[] = [
                    'product_id' => $prod->id,
                    'name' => $prod->name,
                    'qty' => $qty,
                    'unit_price' => (float) $prod->selling_price,
                    'line_total' => $lineTotal,
                ];

                $subtotal += $lineTotal;
                $totalWeightGrams += ($prod->weight_grams * $qty);
            }
        }

        if (empty($cartItems)) {
            return redirect()->route('store.cart.index');
        }

        $shippingOptions = $this->shippingService->getAvailableOptions($totalWeightGrams);

        return view('store.checkout', compact('cartItems', 'subtotal', 'totalWeightGrams', 'shippingOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $sessionCart = session()->get('cart', []);
        if (empty($sessionCart)) {
            return redirect()->route('store.cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'customer_email' => ['nullable', 'email', 'max:100'],
            'fulfillment_type' => ['required', 'in:pickup,delivery'],
            'shipping_courier' => ['nullable', 'string', 'required_if:fulfillment_type,delivery'],
            'customer_address' => ['nullable', 'string', 'required_if:fulfillment_type,delivery'],
            'customer_notes' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', 'in:qris,bank_transfer,cod'],
        ]);

        $cartItems = [];
        foreach ($sessionCart as $pId => $item) {
            $cartItems[] = [
                'product_id' => (int) $pId,
                'qty' => (int) $item['qty'],
            ];
        }

        try {
            $order = $this->onlineOrderService->createFromStorefront($cartItems, $validated);

            // Clear session cart
            session()->forget('cart');

            return redirect()->route('store.payment.show', $order->order_number)
                ->with('success', "Pesanan #{$order->order_number} berhasil dibuat! Silakan selesaikan pembayaran simulasi.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage())->withInput();
        }
    }
}
