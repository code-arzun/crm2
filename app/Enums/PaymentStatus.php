<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case UNPAID = 'unpaid';
    case PARTIAL = 'partial';
    case PAID = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::UNPAID => 'Belum Dibayar',
            self::PARTIAL => 'Dibayar Sebagian',
            self::PAID => 'Lunas',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::UNPAID => 'danger',
            self::PARTIAL => 'warning',
            self::PAID => 'success',
        };
    }
}