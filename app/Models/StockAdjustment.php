<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'adjustment_number',
        'product_id',
        'user_id',
        'adjustment_type',
        'qty_before',
        'qty_change',
        'qty_after',
        'unit_cost',
        'reason',
        'adjusted_at',
    ];

    protected function casts(): array
    {
        return [
            'qty_before' => 'integer',
            'qty_change' => 'integer',
            'qty_after' => 'integer',
            'unit_cost' => 'decimal:2',
            'adjusted_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->adjustment_type) {
            'restock' => 'Stok Masuk (Supplier)',
            'opname' => 'Koreksi Stock Opname',
            'damaged' => 'Barang Rusak / Hilang',
            'return' => 'Retur Konsumen',
            default => ucfirst($this->adjustment_type),
        };
    }
}
