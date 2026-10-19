<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'diskon_persen',
        'min_transaksi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'diskon_persen' => 'decimal:2',
            'min_transaksi' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // ========== RELASI ==========

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'tier_id');
    }

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class, 'tier_id');
    }

    // ========== SCOPE ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}