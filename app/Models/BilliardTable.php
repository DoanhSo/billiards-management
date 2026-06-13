<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BilliardTable extends Model
{
    protected $fillable = [
        'table_number',
        'table_type',
        'price_per_hour',
        'status',
        'description',
    ];

    // =====================
    // Relationships
    // =====================

    /**
     * Bàn có nhiều Booking.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Bàn có nhiều TableSession.
     */
    public function tableSessions(): HasMany
    {
        return $this->hasMany(TableSession::class);
    }

    /**
     * Bàn có nhiều Phụ kiện.
     */
    public function equipments(): HasMany
    {
        return $this->hasMany(TableEquipment::class);
    }

    // =====================
    // Scopes
    // =====================

    /**
     * Lọc bàn theo trạng thái.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Lọc bàn trống (AVAILABLE).
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'AVAILABLE');
    }

    /**
     * Lọc bàn đang chơi (PLAYING).
     */
    public function scopePlaying(Builder $query): Builder
    {
        return $query->where('status', 'PLAYING');
    }

    /**
     * Lọc bàn đang bảo trì (MAINTENANCE).
     */
    public function scopeMaintenance(Builder $query): Builder
    {
        return $query->where('status', 'MAINTENANCE');
    }
}
