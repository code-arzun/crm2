<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'tipe',
        'qty',
        'referensi',
        'keterangan',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'tipe' => StockMovementType::class,
            'qty' => 'integer',
        ];
    }

    // ========== RELASI ==========

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ========== SCOPE ==========

    public function scopeMasuk($query)
    {
        return $query->whereIn('tipe', [
            StockMovementType::IN,
            StockMovementType::RETURN,
        ]);
    }

    public function scopeKeluar($query)
    {
        return $query->where('tipe', StockMovementType::OUT);
    }
}