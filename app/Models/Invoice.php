<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'table_session_id',
        'staff_id',
        'subtotal',
        'discount',
        'discount_percent',
        'total_amount',
        'payment_method',
        'payment_status',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'          => 'decimal:2',
            'discount'          => 'decimal:2',
            'discount_percent'  => 'decimal:2',
            'total_amount'      => 'decimal:2',
        ];
    }

    // =====================
    // Relationships
    // =====================

    /**
     * Invoice thuộc về một TableSession.
     */
    public function tableSession(): BelongsTo
    {
        return $this->belongsTo(TableSession::class);
    }

    /**
     * Invoice thuộc về một nhân viên (User).
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Invoice có nhiều InvoiceDetail.
     */
    public function invoiceDetails(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    // =====================
    // Scopes
    // =====================

    /**
     * Lọc hóa đơn đã thanh toán.
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_status', 'PAID');
    }

    /**
     * Lọc hóa đơn chưa thanh toán.
     */
    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('payment_status', 'UNPAID');
    }

    /**
     * Lọc hóa đơn theo phương thức thanh toán.
     */
    public function scopeByPaymentMethod(Builder $query, string $method): Builder
    {
        return $query->where('payment_method', $method);
    }
}
