<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tipe',
        'kondisi',
        'reward',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'kondisi' => 'array',
            'reward' => 'array',
            'is_active' => 'boolean',
        ];
    }

    // ========== RELASI ==========

    public function transactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    // ========== SCOPE ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTipe($query, string $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    // ========== HELPER ==========

    /**
     * Cek apakah kondisi rule terpenuhi untuk customer tertentu.
     * $kondisi format: ['min_transaksi' => 1000000, 'first_order' => true, ...]
     */
    public function kondisiTerpenuhi(Customer $customer, ?Order $order = null): bool
    {
        $kondisi = $this->kondisi ?? [];

        // Cek min transaksi
        if (isset($kondisi['min_transaksi'])) {
            $totalOrder = $order ? (float) $order->total : 0;
            if ($totalOrder < (float) $kondisi['min_transaksi']) {
                return false;
            }
        }

        // Cek first order
        if (!empty($kondisi['first_order'])) {
            $jumlahOrder = $customer->orders()->count();
            if ($jumlahOrder > 0) {
                return false;
            }
        }

        // Cek member sejak
        if (isset($kondisi['member_sejak_bulan'])) {
            $bulan = (int) $kondisi['member_sejak_bulan'];
            if ($customer->created_at->diffInMonths(now()) < $bulan) {
                return false;
            }
        }

        // Cek tier
        if (isset($kondisi['tier_id'])) {
            if ($customer->tier_id !== (int) $kondisi['tier_id']) {
                return false;
            }
        }

        return true;
    }
}