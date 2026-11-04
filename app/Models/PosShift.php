<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_code',
        'user_id',
        'shift_name',
        'opening_cash',
        'cash_sales_total',
        'non_cash_sales_total',
        'petty_cash_out_total',
        'closing_cash_expected',
        'closing_cash_actual',
        'difference_amount',
        'status',
        'notes',
        'opened_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'opening_cash' => 'decimal:2',
            'cash_sales_total' => 'decimal:2',
            'non_cash_sales_total' => 'decimal:2',
            'petty_cash_out_total' => 'decimal:2',
            'closing_cash_expected' => 'decimal:2',
            'closing_cash_actual' => 'decimal:2',
            'difference_amount' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cashbookEntries(): HasMany
    {
        return $this->hasMany(CashbookEntry::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
