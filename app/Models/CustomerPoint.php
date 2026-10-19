<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'total_poin',
    ];

    protected function casts(): array
    {
        return [
            'total_poin' => 'integer',
        ];
    }

    // ========== RELASI ==========

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // ========== HELPER ==========

    /**
     * Tambah poin (positif) atau kurangi (negatif).
     */
    public function adjust(int $delta): self
    {
        $this->total_poin = max(0, $this->total_poin + $delta);
        $this->save();

        return $this;
    }
}