<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'slug',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ========== RELASI ==========

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // ========== SCOPE ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ========== HELPER ==========

    /**
     * Auto-generate slug dari nama saat creating.
     */
    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = \Str::slug($category->nama);
            }
        });
    }
}