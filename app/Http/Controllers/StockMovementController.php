<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(Request $request): View
    {
        $productId = $request->input('product_id');
        $movementType = $request->input('movement_type');

        $query = StockMovement::with(['product', 'user'])->latest('id');

        if ($productId) {
            $query->where('product_id', $productId);
        }

        if ($movementType) {
            $query->where('movement_type', $movementType);
        }

        $movements = $query->paginate(25)->withQueryString();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $selectedProduct = $productId ? Product::find($productId) : null;

        return view('warehouse.movements.index', compact('movements', 'products', 'productId', 'movementType', 'selectedProduct'));
    }
}
