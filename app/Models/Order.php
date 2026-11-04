<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'client_tx_id',
        'channel',
        'status',
        'payment_status',
        'payment_method',
        'cashier_id',
        'pos_shift_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'fulfillment_type',
        'shipping_courier',
        'shipping_service',
        'tracking_number',
        'customer_address',
        'customer_notes',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'shipping_cost',
        'grand_total',
        'cash_received',
        'change_amount',
        'print_count',
        'is_offline_synced',
        'ordered_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'cash_received' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'print_count' => 'integer',
            'is_offline_synced' => 'boolean',
            'ordered_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function posShift(): BelongsTo
    {
        return $this->belongsTo(PosShift::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(PaymentTransaction::class)->latestOfMany();
    }

    public function printLogs(): HasMany
    {
        return $this->hasMany(PrintLog::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function cashbookEntries(): HasMany
    {
        return $this->hasMany(CashbookEntry::class);
    }

    public function isPos(): bool
    {
        return $this->channel === 'pos';
    }

    public function isOnline(): bool
    {
        return $this->channel === 'online';
    }

    public function getShippingTrackingNumberAttribute(): ?string
    {
        return $this->tracking_number;
    }

    public function setShippingTrackingNumberAttribute(?string $value): void
    {
        $this->attributes['tracking_number'] = $value;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getFormattedTotalAttribute(): string
    {
        return format_rupiah($this->grand_total);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending_payment' => ['label' => 'Menunggu Pembayaran', 'class' => 'bg-amber-100 text-amber-800 border-amber-200'],
            'pending_confirmation' => ['label' => 'Menunggu Konfirmasi', 'class' => 'bg-blue-100 text-blue-800 border-blue-200'],
            'paid' => ['label' => 'Lunas', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
            'processing' => ['label' => 'Sedang Diproses / Disiapkan', 'class' => 'bg-indigo-100 text-indigo-800 border-indigo-200'],
            'ready_for_pickup' => ['label' => 'Siap Diambil di Kasir', 'class' => 'bg-purple-100 text-purple-800 border-purple-200'],
            'shipped' => ['label' => 'Sedang Dikirim', 'class' => 'bg-cyan-100 text-cyan-800 border-cyan-200'],
            'completed' => ['label' => 'Selesai', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
            'cancelled' => ['label' => 'Dibatalkan', 'class' => 'bg-rose-100 text-rose-800 border-rose-200'],
            'refunded' => ['label' => 'Di-refund', 'class' => 'bg-slate-100 text-slate-800 border-slate-200'],
            default => ['label' => ucfirst($this->status), 'class' => 'bg-slate-100 text-slate-800 border-slate-200'],
        };
    }
}
