<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'nomor_invoice',
        'pdf_path',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    // ========== RELASI ==========

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ========== HELPER ==========

    /**
     * Generate nomor invoice.
     * Format default: INV/YYYYMM/0001
     * Nanti bisa dibuat configurable via settings.
     */
    public static function generateNomor(): string
    {
        $periode = now()->format('Ym');
        $last = static::where('nomor_invoice', 'like', "INV/{$periode}/%")->count() + 1;

        return sprintf('INV/%s/%04d', $periode, $last);
    }

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->nomor_invoice)) {
                $invoice->nomor_invoice = static::generateNomor();
            }
            if (empty($invoice->issued_at)) {
                $invoice->issued_at = now();
            }
        });
    }
}