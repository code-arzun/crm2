<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case SIAP_KIRIM = 'siap_kirim';
    case DIKIRIM = 'dikirim';
    case DITERIMA = 'diterima';

    public function label(): string
    {
        return match ($this) {
            self::SIAP_KIRIM => 'Siap Kirim',
            self::DIKIRIM => 'Dikirim',
            self::DITERIMA => 'Diterima',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::SIAP_KIRIM => 'secondary',
            self::DIKIRIM => 'primary',
            self::DITERIMA => 'success',
        };
    }
}