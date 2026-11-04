<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryService
{
    /**
     * Atomically record stock movement and update product stock.
     */
    public function recordMovement(
        Product $product,
        int $qtyChange,
        string $movementType,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null,
        ?int $userId = null
    ): StockMovement {
        return DB::transaction(function () use ($product, $qtyChange, $movementType, $referenceType, $referenceId, $notes, $userId) {
            // Lock product record for update to prevent race condition
            $lockedProduct = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();

            $qtyBefore = $lockedProduct->stock_qty;
            $qtyAfter = $qtyBefore + $qtyChange;

            // Allow clamp to 0 if needed for offline sync discrepancy, but prevent negative in regular sales
            if ($qtyAfter < 0 && $movementType !== 'pos_sale_offline_sync') {
                throw new \InvalidArgumentException("Stok tidak mencukupi untuk {$lockedProduct->name}. Tersedia: {$qtyBefore}, diminta: " . abs($qtyChange));
            }

            $lockedProduct->stock_qty = $qtyAfter;
            $lockedProduct->save();

            return StockMovement::create([
                'product_id' => $lockedProduct->id,
                'user_id' => $userId,
                'movement_type' => $movementType,
                'qty_before' => $qtyBefore,
                'qty_change' => $qtyChange,
                'qty_after' => $qtyAfter,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
            ]);
        });
    }

    /**
     * Perform formal warehouse stock adjustment (restock, opname, damaged, etc.)
     */
    public function adjustStock(
        Product $product,
        int $qtyChange,
        string $adjustmentType,
        float $unitCost,
        string $reason,
        User $user
    ): StockAdjustment {
        return DB::transaction(function () use ($product, $qtyChange, $adjustmentType, $unitCost, $reason, $user) {
            $lockedProduct = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();

            $qtyBefore = $lockedProduct->stock_qty;
            $qtyAfter = $qtyBefore + $qtyChange;

            $adjustmentNumber = 'ADJ-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            $adjustment = StockAdjustment::create([
                'adjustment_number' => $adjustmentNumber,
                'product_id' => $lockedProduct->id,
                'user_id' => $user->id,
                'adjustment_type' => $adjustmentType,
                'qty_before' => $qtyBefore,
                'qty_change' => $qtyChange,
                'qty_after' => $qtyAfter,
                'unit_cost' => $unitCost,
                'reason' => $reason,
                'adjusted_at' => now(),
            ]);

            $movementType = match ($adjustmentType) {
                'restock' => 'restock',
                'opname' => 'opname_adjustment',
                'damaged' => 'damaged',
                'return' => 'return_refund',
                default => 'opname_adjustment',
            };

            $this->recordMovement(
                $lockedProduct,
                $qtyChange,
                $movementType,
                'adjustment',
                $adjustment->id,
                $reason,
                $user->id
            );

            return $adjustment;
        });
    }
}
