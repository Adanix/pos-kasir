<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashbookEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_number',
        'entry_date',
        'transaction_at',
        'type',
        'category',
        'payment_method',
        'amount',
        'balance_after',
        'description',
        'reference_number',
        'order_id',
        'pos_shift_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'transaction_at' => 'datetime',
            'amount' => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function posShift(): BelongsTo
    {
        return $this->belongsTo(PosShift::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isIn(): bool
    {
        return $this->type === 'in';
    }

    public function isOut(): bool
    {
        return $this->type === 'out';
    }

    public function getFormattedAmountAttribute(): string
    {
        return format_rupiah($this->amount);
    }

    public function getFormattedBalanceAttribute(): string
    {
        return format_rupiah($this->balance_after);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'modal_awal' => 'Modal Awal Kas Toko',
            'penjualan_pos' => 'Penjualan Kasir (POS)',
            'penjualan_online' => 'Penjualan Toko Online',
            'operasional_toko' => 'Biaya Operasional Toko',
            'pembelian_stok' => 'Pembelian Stok / Restock',
            'gaji_staf' => 'Gaji / Uang Makan Staf',
            'refund_penjualan' => 'Refund / Retur Penjualan',
            'lainnya' => 'Lain-lain',
            default => ucfirst(str_replace('_', ' ', $this->category)),
        };
    }
}
