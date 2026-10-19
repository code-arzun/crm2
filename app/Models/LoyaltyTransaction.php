<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'loyalty_rule_id',
        'order_id',
        'tipe',
        'poin',
        'nominal',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'poin' => 'integer',
            'nominal' => 'decimal:2',
        ];
    }

    // ========== RELASI ==========

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(LoyaltyRule::class, 'loyalty_rule_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ========== SCOPE ==========

    public function scopeEarn($query)
    {
        return $query->where('tipe', 'earn');
    }

    public function scopeRedeem($query)
    {
        return $query->where('tipe', 'redeem');
    }
}