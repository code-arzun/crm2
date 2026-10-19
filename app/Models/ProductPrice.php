<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'customer_id',
        'tier_id',
        'harga',
        'berlaku_dari',
        'berlaku_sampai',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'berlaku_dari' => 'date',
            'berlaku_sampai' => 'date',
        ];
    }

    // ========== RELASI ==========

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function tier(): BelongsTo
    {
        return $this->belongsTo(CustomerTier::class, 'tier_id');
    }

    // ========== SCOPE ==========

    /**
     * Harga yang masih berlaku (dalam rentang tanggal).
     */
    public function scopeBerlaku($query)
    {
        $now = now()->toDateString();

        return $query
            ->where(fn ($q) => $q->whereNull('berlaku_dari')->orWhere('berlaku_dari', '<=', $now))
            ->where(fn ($q) => $q->whereNull('berlaku_sampai')->orWhere('berlaku_sampai', '>=', $now));
    }

    public function scopeForCustomer($query, Customer $customer)
    {
        return $query->where('customer_id', $customer->id);
    }

    public function scopeForTier($query, int $tierId)
    {
        return $query->where('tier_id', $tierId);
    }

    // ========== HELPER ==========

    public function isBerlaku(): bool
    {
        $now = now()->toDateString();

        if ($this->berlaku_dari && $this->berlaku_dari->toDateString() > $now) {
            return false;
        }

        if ($this->berlaku_sampai && $this->berlaku_sampai->toDateString() < $now) {
            return false;
        }

        return true;
    }

    /**
     * Label target harga: "Customer: Budi" / "Tier: Gold" / "Global".
     */
    public function targetLabel(): string
    {
        if ($this->customer_id) {
            return 'Customer: ' . optional($this->customer)->nama_lengkap;
        }

        if ($this->tier_id) {
            return 'Tier: ' . optional($this->tier)->nama;
        }

        return 'Global';
    }
}