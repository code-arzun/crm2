<?php

namespace App\Models;

use App\Enums\ReturnStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnOrder extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'order_id',
        'order_item_id',
        'qty',
        'alasan',
        'status',
        'refund_amount',
        'approved_by',
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
}