<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sku',
        'barcode',
        'name',
        'slug',
        'description',
        'unit',
        'cost_price',
        'selling_price',
        'stock_qty',
        'min_stock_alert',
        'weight_grams',
        'image_url',
        'is_active',
        'is_featured_online',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'stock_qty' => 'integer',
            'min_stock_alert' => 'integer',
            'weight_grams' => 'integer',
            'is_active' => 'boolean',
            'is_featured_online' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class)->latest();
    }

    public function isLowStock(): bool
    {
        return $this->stock_qty <= $this->min_stock_alert && $this->stock_qty > 0;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_qty <= 0;
    }

    public function getFormattedPriceAttribute(): string
    {
        return format_rupiah($this->selling_price);
    }

    public function getFormattedCostAttribute(): string
    {
        return format_rupiah($this->cost_price);
    }

    // Aliases for compatibility
    public function getStockAttribute(): int
    {
        return (int) $this->stock_qty;
    }

    public function setStockAttribute($value): void
    {
        $this->attributes['stock_qty'] = $value;
    }

    public function getMinStockAttribute(): int
    {
        return (int) $this->min_stock_alert;
    }

    public function setMinStockAttribute($value): void
    {
        $this->attributes['min_stock_alert'] = $value;
    }

    public function getIsOnlineListedAttribute(): bool
    {
        return (bool) $this->is_featured_online;
    }

    public function setIsOnlineListedAttribute($value): void
    {
        $this->attributes['is_featured_online'] = $value;
    }
}
