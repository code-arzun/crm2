<?php

namespace App\Models;

use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'kode',
        'nama',
        'tipe',
        'harga_default',
        'stok',
        'satuan',
        'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tipe' => ProductType::class,
            'harga_default' => 'decimal:2',
            'stok' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // ========== RELASI ==========

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ========== SCOPE ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBarang($query)
    {
        return $query->where('tipe', ProductType::BARANG);
    }

    public function scopeJasa($query)
    {
        return $query->where('tipe', ProductType::JASA);
    }

    // ========== HELPER ==========

    /**
     * Ambil harga untuk customer tertentu.
     * Prioritas:
     *   1. Harga khusus customer (product_prices.customer_id)
     *   2. Harga khusus tier customer (product_prices.tier_id)
     *   3. Harga default produk
     *
     * Hanya ambil product_prices yang masih berlaku (berlaku_dari/sampai).
     */
    public function hargaUntukCustomer(?Customer $customer = null): float
    {
        if (!$customer) {
            return (float) $this->harga_default;
        }

        // 1. Cek harga khusus customer
        $customerPrice = $this->productPrices
            ->where('customer_id', $customer->id)
            ->where(fn ($p) => $this->filterBerlaku($p))
            ->sortByDesc('harga')
            ->first();

        if ($customerPrice) {
            return (float) $customerPrice->harga;
        }

        // 2. Cek harga khusus tier
        if ($customer->tier_id) {
            $tierPrice = $this->productPrices
                ->where('tier_id', $customer->tier_id)
                ->where(fn ($p) => $this->filterBerlaku($p))
                ->first();

            if ($tierPrice) {
                return (float) $tierPrice->harga;
            }
        }

        // 3. Default
        return (float) $this->harga_default;
    }

    /**
     * Filter product_price yang masih berlaku (tanggal).
     */
    private function filterBerlaku(ProductPrice $price): bool
    {
        $now = now()->toDateString();

        if ($price->berlaku_dari && $price->berlaku_dari->toDateString() > $now) {
            return false;
        }

        if ($price->berlaku_sampai && $price->berlaku_sampai->toDateString() < $now) {
            return false;
        }

        return true;
    }

    /**
     * Total stok dari semua warehouse.
     * (nanti saat multi-warehouse: sum dari tabel stocks)
     */
    public function totalStok(): int
    {
        // Sementara pakai kolom products.stok sebagai cache
        // Saat multi-warehouse aktif, ganti dengan:
        // return (int) $this->stocks()->sum('qty');
        return (int) $this->stok;
    }

    /**
     * Apakah stok cukup untuk qty tertentu?
     */
    public function stokCukup(int $qty): bool
    {
        if (!$this->tipe->managesStock()) {
            return true;
        }

        return $this->totalStok() >= $qty;
    }
}