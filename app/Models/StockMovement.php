<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'movement_type',
        'qty_before',
        'qty_change',
        'qty_after',
        'reference_type',
        'reference_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'qty_before' => 'integer',
            'qty_change' => 'integer',
            'qty_after' => 'integer',
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
        return match ($this->movement_type) {
            'initial' => 'Stok Awal',
            'pos_sale' => 'Penjualan Kasir (POS)',
            'online_reserve' => 'Reservasi Toko Online',
            'online_release' => 'Batal Online (Kembali)',
            'restock' => 'Stok Masuk (Restock)',
            'opname_adjustment' => 'Koreksi Stock Opname',
            'damaged' => 'Barang Rusak / Kadaluwarsa',
            'return_refund' => 'Retur / Refund Penjualan',
            default => ucfirst(str_replace('_', ' ', $this->movement_type)),
        };
    }
}
