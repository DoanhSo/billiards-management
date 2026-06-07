<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TableSession extends Model
{
    protected $fillable = [
        'billiard_table_id',
        'customer_id',
        'start_time',
        'end_time',
        'total_hours',
        'table_price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_time'  => 'datetime',
            'end_time'    => 'datetime',
            'total_hours' => 'decimal:2',
            'table_price' => 'decimal:2',
        ];
    }

    // =====================
    // Relationships
    // =====================

    /**
     * TableSession thuộc về một BilliardTable.
     */
    public function billiardTable(): BelongsTo
    {
        return $this->belongsTo(BilliardTable::class);
    }

    /**
     * TableSession thuộc về một khách hàng (User).
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * TableSession có một Invoice.
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    // =====================
    // Scopes
    // =====================

    /**
     * Lọc phiên đang chơi.
     */
    public function scopePlaying(Builder $query): Builder
    {
        return $query->where('status', 'PLAYING');
    }

    /**
     * Lọc phiên đã kết thúc.
     */
    public function scopeFinished(Builder $query): Builder
    {
        return $query->where('status', 'FINISHED');
    }
}
