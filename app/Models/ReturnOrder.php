<?php

namespace App\Models;

use App\Enums\ReturnStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnOrder extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'kode_retur',
        'total_qty_diretur',
        'order_id',
        'qty',
        'alasan',
        'status',
        'refund_amount',
        'approved_by',
        'alasan_retur',
        'metode_retur',
        'biaya_retur',
        'refund_method',
        'refund_proof',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReturnStatus::class,
            'qty' => 'integer',
            'refund_amount' => 'decimal:2',
        ];
    }

    // ========== RELASI ==========

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ========== HELPER ==========

    public function setujui(User $user, float $refundAmount = 0): self
    {
        $this->update([
            'status' => ReturnStatus::DISETUJUI,
            'approved_by' => $user->id,
            'refund_amount' => $refundAmount,
        ]);

        return $this;
    }

    public function tolak(User $user): self
    {
        $this->update([
            'status' => ReturnStatus::DITOLAK,
            'approved_by' => $user->id,
        ]);

        return $this;
    }

    protected static function booted(): void
    {
        static::creating(function (ReturnOrder $return) {
            // Jika kode_retur belum diisi, buat otomatis
            if (empty($return->kode_retur)) {
                // Ambil data order terkait
                $order = \App\Models\Order::find($return->order_id);
                
                if ($order) {
                    // Hitung ini retur ke berapa untuk order tersebut
                    $urutan = static::where('order_id', $return->order_id)->count() + 1;
                    
                    // Bersihkan kode order dari garis miring (misal: ORD/2023/0001 jadi ORD20230001)
                    $kodeOrder = str_replace('/', '', $order->kode_order);
                    
                    // Format: RTR-ORD20230001-1
                    $return->kode_retur = 'RTR-' . $kodeOrder . '-' . $urutan;
                } else {
                    // Fallback jika order tidak ditemukan (harusnya tidak terjadi)
                    $return->kode_retur = 'RTR-' . time();
                }
            }
        });
    }
}