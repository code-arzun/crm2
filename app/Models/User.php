<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'telepon',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ========== RELASI ==========

    /**
     * User yang role-nya 'customer' punya 1 profil customer.
     */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    /**
     * Order yang di-handle user ini sebagai sales.
     */
    public function salesOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'sales_id');
    }

    /**
     * Approval yang dilakukan user ini (admin/finance).
     */
    public function orderApprovals(): HasMany
    {
        return $this->hasMany(OrderApproval::class);
    }

    /**
     * Lead yang di-handle user ini sebagai sales.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'sales_id');
    }

    /**
     * Customer yang di-handle user ini sebagai sales PIC.
     */
    public function handledCustomers(): HasMany
    {
        return $this->hasMany(Customer::class, 'sales_id');
    }

    // ========== HELPER ==========

    public function isSuperadmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isSales(): bool
    {
        return $this->hasRole('sales');
    }

    public function isFinance(): bool
    {
        return $this->hasRole('finance');
    }

    public function isWarehouse(): bool
    {
        return $this->hasRole('warehouse');
    }

    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    /**
     * Apakah user ini termasuk staff (bukan customer).
     */
    public function isStaff(): bool
    {
        return !$this->isCustomer();
    }
}