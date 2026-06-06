<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'price',
        'quantity',
        'image',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price'  => 'decimal:2',
            'status' => 'boolean',
        ];
    }

    // =====================
    // Relationships
    // =====================

    /**
     * Sản phẩm thuộc về một Danh mục.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Sản phẩm có nhiều InvoiceDetail.
     */
    public function invoiceDetails(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    // =====================
    // Scopes
    // =====================

    /**
     * Lọc sản phẩm đang bán.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Lọc sản phẩm còn hàng.
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('quantity', '>', 0);
    }
}
