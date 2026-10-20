<?php

namespace App\Models;

use App\Enums\OrderChannel;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_order',
        'customer_id',
        'sales_id',
        'jalur',
        'status',
        'payment_type',
        'payment_status',
        'subtotal',
        'diskon',
        'ppn',
        'total',
        'due_date',
        'catatan',
        'alasan_batal',
    ];

    protected function casts(): array
    {
        return [
            'jalur' => OrderChannel::class,
            'status' => OrderStatus::class,
            'payment_type' => PaymentType::class,
            'payment_status' => PaymentStatus::class,
            'subtotal' => 'decimal:2',
            'diskon' => 'decimal:2',
            'ppn' => 'decimal:2',
            'total' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    // ========== RELASI ==========

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(OrderApproval::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(ReturnOrder::class);
    }

    // ========== SCOPE ==========

    public function scopeStatus($query, OrderStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeMenungguApproval($query)
    {
        return $query->where('status', OrderStatus::MENUNGGU_APPROVAL);
    }

    public function scopeBelumLunas($query)
    {
        return $query->whereIn('payment_status', [
            PaymentStatus::UNPAID,
            PaymentStatus::PARTIAL,
        ]);
    }

    public function scopeSelfOrder($query)
    {
        return $query->where('jalur', OrderChannel::SELF_ORDER);
    }

    public function scopeSalesOrder($query)
    {
        return $query->where('jalur', OrderChannel::SALES_ORDER);
    }

    // ========== STATUS MACHINE ==========

    public function bisaDiApprove(): bool
    {
        return $this->status === OrderStatus::MENUNGGU_APPROVAL
            && $this->jalur === OrderChannel::SALES_ORDER;
    }

    public function bisaDibatalkan(): bool
    {
        return in_array($this->status, [
            OrderStatus::DRAFT,
            OrderStatus::MENUNGGU_APPROVAL,
            OrderStatus::DIKONFIRMASI,
        ], true);
    }

    public function bisaDikirim(): bool
    {
        return $this->status === OrderStatus::DIPROSES;
    }

    public function bisaDiretur(): bool
    {
        return in_array($this->status, [
            OrderStatus::DIKIRIM,
            OrderStatus::SELESAI,
        ], true);
    }

    // ========== PERHITUNGAN ==========

    /**
     * Hitung ulang subtotal, ppn, total dari items.
     * $ppnPersen: 0 kalau PPN off, 11 kalau on.
     */
    public function recalculate(float $ppnPersen = 0): self
    {
        $subtotal = $this->items()->sum('subtotal');
        $diskon = (float) $this->diskon;
        $dpp = max(0, $subtotal - $diskon); // dasar pengenaan pajak
        $ppn = round($dpp * ($ppnPersen / 100), 2);
        $total = $dpp + $ppn;

        $this->update([
            'subtotal' => $subtotal,
            'ppn' => $ppn,
            'total' => $total,
        ]);

        return $this;
    }

    /**
     * Hitung sisa tagihan (total - total pembayaran verified).
     */
    public function sisaTagihan(): float
    {
        $dibayar = $this->payments()
            ->where('status', \App\Enums\PaymentVerificationStatus::VERIFIED)
            ->sum('jumlah');

        return max(0, (float) $this->total - (float) $dibayar);
    }

    /**
     * Update payment_status otomatis dari total pembayaran verified.
     */
    public function syncPaymentStatus(): self
    {
        $dibayar = $this->payments()
            ->where('status', \App\Enums\PaymentVerificationStatus::VERIFIED)
            ->sum('jumlah');

        $status = match (true) {
            $dibayar <= 0 => PaymentStatus::UNPAID,
            $dibayar < (float) $this->total => PaymentStatus::PARTIAL,
            default => PaymentStatus::PAID,
        };

        $this->update(['payment_status' => $status]);

        return $this;
    }

    // ========== HELPER ==========

    public static function generateKode(): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $last = static::whereDate('created_at', today())->count() + 1;

        return sprintf('%s/%s/%04d', $prefix, $date, $last);
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->kode_order)) {
                $order->kode_order = static::generateKode();
            }
        });
    }
}