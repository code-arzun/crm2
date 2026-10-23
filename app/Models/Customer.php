<?php

namespace App\Models;

use App\Enums\CustomerSegment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'tier_id',
        'sales_id',
        'nama_lengkap',
        'telepon',
        'alamat',
        'nama_perusahaan',
        'kategori_bisnis',
        'segment',
        'total_transaksi',
        'last_order_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'segment' => CustomerSegment::class,
            'total_transaksi' => 'decimal:2',
            'last_order_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // ========== RELASI ==========

    /**
     * Akun login customer (kalau register mandiri).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tier customer (Bronze/Silver/Gold).
     */
    public function tier(): BelongsTo
    {
        return $this->belongsTo(CustomerTier::class, 'tier_id');
    }

    /**
     * Sales PIC yang handle customer ini.
     */
    public function sales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function loyaltyTransactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function points(): HasMany
    {
        return $this->hasMany(CustomerPoint::class);
    }

    /**
     * Lead asal customer ini (kalau dari konversi lead).
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    // ========== SCOPE ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySegment($query, CustomerSegment $segment)
    {
        return $query->where('segment', $segment);
    }

    // ========== HELPER ==========

    /**
     * Hitung ulang segmentasi berdasarkan aktivitas.
     */
    public function recalculateSegment(): CustomerSegment
    {
        $now = now();
        $lastOrder = $this->last_order_at;
        $totalOrder = $this->orders()->count();
        $memberSince = $this->created_at;

        $segment = match (true) {
            // Dormant: tidak order > 6 bulan
            $lastOrder && $lastOrder->lt($now->copy()->subMonths(6)) => CustomerSegment::DORMANT,
            // Loyal: member > 1 tahun & > 10 order
            $memberSince->lt($now->copy()->subYear()) && $totalOrder > 10 => CustomerSegment::LOYAL,
            // Aktif: order dalam 3 bulan terakhir
            $lastOrder && $lastOrder->gt($now->copy()->subMonths(3)) => CustomerSegment::ACTIVE,
            // Baru: default
            default => CustomerSegment::NEW,
        };

        $this->update(['segment' => $segment]);

        return $segment;
    }

    /**
     * Total poin customer saat ini.
     */
    public function totalPoints(): int
    {
        return (int) $this->points()->sum('total_poin');
    }
}