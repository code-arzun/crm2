<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'qty',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
        ];
    }

    // ========== RELASI ==========

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Nanti saat warehouse dibuat:
    // public function warehouse(): BelongsTo
    // {
    //     return $this->belongsTo(Warehouse::class);
    // }

    // ========== HELPER ==========

    /**
     * Tambah stok (positif) atau kurangi (negatif).
     * Update qty di tabel stocks + sinkron ke products.stok.
     */
    public function adjust(int $delta, string $referensi = null, string $keterangan = null): self
    {
        $this->qty += $delta;
        $this->save();

        // Sinkron ke cache products.stok
        $this->syncProductStok();

        // Catat movement
        StockMovement::create([
            'product_id' => $this->product_id,
            'warehouse_id' => $this->warehouse_id,
            'tipe' => $delta >= 0 ? \App\Enums\StockMovementType::IN : \App\Enums\StockMovementType::OUT,
            'qty' => abs($delta),
            'referensi' => $referensi,
            'keterangan' => $keterangan,
            'user_id' => auth()->id(),
        ]);

        return $this;
    }

    /**
     * Sinkron total qty semua warehouse ke products.stok (cache).
     */
    public function syncProductStok(): void
    {
        $total = static::where('product_id', $this->product_id)->sum('qty');
        $this->product()->update(['stok' => $total]);
    }
}