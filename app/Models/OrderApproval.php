<?php

namespace App\Models;

use App\Enums\ApprovalAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'aksi',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'aksi' => ApprovalAction::class,
        ];
    }

    // ========== RELASI ==========

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}