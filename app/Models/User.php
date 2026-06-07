<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'phone',
        'avatar',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'status'            => 'boolean',
        ];
    }

    // =====================
    // Relationships
    // =====================

    /**
     * User thuộc về một Role.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * User có nhiều Booking.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * User (khách) có nhiều TableSession (customer_id).
     */
    public function tableSessions(): HasMany
    {
        return $this->hasMany(TableSession::class, 'customer_id');
    }

    /**
     * User (nhân viên) có nhiều Invoice (staff_id).
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'staff_id');
    }

    // =====================
    // Role Helpers
    // =====================

    /**
     * Kiểm tra user có phải Admin không.
     */
    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    /**
     * Kiểm tra user có phải Nhân viên không.
     */
    public function isStaff(): bool
    {
        return $this->role?->name === 'staff';
    }

    /**
     * Kiểm tra user có phải Khách hàng không.
     */
    public function isCustomer(): bool
    {
        return $this->role?->name === 'customer';
    }

    // =====================
    // Scopes
    // =====================

    /**
     * Lọc user đang hoạt động.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Lọc user bị khóa.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', false);
    }
}
