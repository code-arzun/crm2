<?php

namespace App\Models;

use App\Enums\PaymentType;
use App\Enums\PaymentVerificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'metode',
        'jumlah',
        'bukti_transfer',
        'status',
        'verified_by',
        'verified_at',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'metode' => PaymentType::class,
            'jumlah' => 'decimal:2',
            'status' => PaymentVerificationStatus::class,
            'verified_at' => 'datetime',
        ];
    }

    // ========== RELASI ==========

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ========== HELPER ==========

    public function isPending(): bool
    {
        return $this->status === PaymentVerificationStatus::PENDING;
    }

    public function isVerified(): bool
    {
        return $this->status === PaymentVerificationStatus::VERIFIED;
    }

    public function verify(User $user): self
    {
        $this->update([
            'status' => PaymentVerificationStatus::VERIFIED,
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        $this->order->syncPaymentStatus();

        return $this;
    }

    public function reject(User $user, string $catatan = null): self
    {
        $this->update([
            'status' => PaymentVerificationStatus::REJECTED,
            'verified_by' => $user->id,
            'verified_at' => now(),
            'catatan' => $catatan,
        ]);

        return $this;
    }
}