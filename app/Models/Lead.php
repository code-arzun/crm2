<?php

namespace App\Models;

use App\Enums\LeadStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_id',
        'customer_id',
        'nama',
        'telepon',
        'email',
        'nama_perusahaan',
        'sumber',
        'status',
        'catatan',
        'converted_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => LeadStatus::class,
            'converted_at' => 'datetime',
        ];
    }

    // ========== RELASI ==========

    public function sales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    // ========== SCOPE ==========

    public function scopeStatus($query, LeadStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAktif($query)
    {
        return $query->whereNotIn('status', [
            LeadStatus::WON,
            LeadStatus::LOST,
        ]);
    }

    public function scopeMilikSales($query, User $sales)
    {
        return $query->where('sales_id', $sales->id);
    }

    // ========== HELPER ==========

    public function isFinal(): bool
    {
        return $this->status->isFinal();
    }

    public function bisaDiKonversi(): bool
    {
        return $this->status === LeadStatus::WON
            && $this->customer_id === null;
    }
}