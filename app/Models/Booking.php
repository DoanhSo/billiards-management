<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'billiard_table_id',
        'booking_date',
        'start_time',
        'end_time',
        'status',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'start_time'   => 'datetime',
            'end_time'     => 'datetime',
        ];
    }

    // =====================
    // Relationships
    // =====================

    /**
     * Booking thuộc về một User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Booking thuộc về một BilliardTable.
     */
    public function billiardTable(): BelongsTo
    {
        return $this->belongsTo(BilliardTable::class);
    }

    // =====================
    // Scopes
    // =====================

    /**
     * Lọc booking theo trạng thái.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Lọc booking đang chờ xác nhận.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'PENDING');
    }

    /**
     * Lọc booking đã xác nhận.
     */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'CONFIRMED');
    }
}
