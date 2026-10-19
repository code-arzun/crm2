<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'user_id',
        'tipe',
        'keterangan',
        'activity_at',
    ];

    protected function casts(): array
    {
        return [
            'activity_at' => 'datetime',
        ];
    }

    // ========== RELASI ==========

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ========== SCOPE ==========

    public function scopeTerbaru($query, int $limit = 10)
    {
        return $query->orderByDesc('activity_at')->limit($limit);
    }
}