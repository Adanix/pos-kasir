<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StockAdjustmentController extends Controller
{
    public function index(Request $request): View
    {
        $productId = $request->input('product_id');
        $adjustmentType = $request->input('adjustment_type');

        $query = StockAdjustment::with(['product', 'user'])->latest('adjusted_at');

        if ($productId) {
            $query->where('product_id', $productId);
        }

        if ($adjustmentType) {
            $query->where('adjustment_type', $adjustmentType);
        }

        $adjustments = $query->paginate(20)->withQueryString();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $selectedProduct = $productId ? Product::find($productId) : null;

        return view('warehouse.adjustments.index', compact('adjustments', 'products', 'productId', 'adjustmentType', 'selectedProduct'));
    }

    public function store(Request $request, InventoryService $inventoryService): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'adjustment_type' => ['required', 'in:restock,opname,damaged,return'],
            'qty_change' => ['required', 'integer'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $unitCost = (float) ($validated['unit_cost'] ?? $product->cost_price);

        // For damaged items, ensure qty_change is negative
        if ($validated['adjustment_type'] === 'damaged' && $validated['qty_change'] > 0) {
            $validated['qty_change'] = -abs($validated['qty_change']);
        }

        // For restock, ensure qty_change is positive
        if ($validated['adjustment_type'] === 'restock' && $validated['qty_change'] < 0) {
            $validated['qty_change'] = abs($validated['qty_change']);
        }

        try {
            $inventoryService->adjustStock(
                $product,
                (int) $validated['qty_change'],
                $validated['adjustment_type'],
                $unitCost,
                $validated['reason'],
                Auth::user()
            );

            return redirect()->route('warehouse.adjustments.index')
                ->with('success', "Penyesuaian stok untuk {$product->name} berhasil dicatat.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses penyesuaian: ' . $e->getMessage())->withInput();
        }
    }
}
