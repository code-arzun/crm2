<?php

namespace App\Models;

use App\Models\ShipmentItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'nama_produk',
        'qty',
        'qty_terkirim',
        'harga',
        'diskon',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'harga' => 'decimal:2',
            'diskon' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    // ========== RELASI ==========

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ========== HELPER ==========

    /**
     * Hitung subtotal dari qty x harga - diskon.
     */
    public function hitungSubtotal(): float
    {
        return max(0, ($this->qty * (float) $this->harga) - (float) $this->diskon);
    }

    protected static function booted(): void
    {
        static::saving(function (OrderItem $item) {
            $item->subtotal = $item->hitungSubtotal();
        });
    }

    public function shipmentItems(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    // Tambahkan helper untuk cek sisa
    public function sisaBelumDikirim(): int
    {
        return $this->qty - $this->qty_terkirim;
    }
}