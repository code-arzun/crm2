<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'tipe',
    ];

    // ========== HELPER ==========

    /**
     * Ambil nilai setting dengan fallback default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->tipe);
    }

    /**
     * Set nilai setting (update atau create).
     */
    public static function set(string $key, mixed $value, string $tipe = 'string'): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : (string) $value,
                'tipe' => $tipe,
            ]
        );
    }

    /**
     * Cast value berdasarkan tipe.
     */
    protected static function castValue(?string $value, string $tipe): mixed
    {
        return match ($tipe) {
            'bool', 'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number', 'int', 'integer' => (int) $value,
            'float', 'decimal' => (float) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }
}