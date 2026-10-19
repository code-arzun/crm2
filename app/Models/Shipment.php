<?php

namespace App\Models;

use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'ekspedisi',
        'no_resi',
        'alamat_kirim',
        'status',
        'shipped_at',
        'received_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ShipmentStatus::class,
            'shipped_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    // ========== RELASI ==========

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ========== HELPER ==========

    public function tandaiDikirim(string $noResi = null): self
    {
        $this->update([
            'status' => ShipmentStatus::DIKIRIM,
            'no_resi' => $noResi ?? $this->no_resi,
            'shipped_at' => now(),
        ]);

        return $this;
    }

    public function tandaiDiterima(): self
    {
        $this->update([
            'status' => ShipmentStatus::DITERIMA,
            'received_at' => now(),
        ]);

        return $this;
    }
}