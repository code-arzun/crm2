<?php

namespace App\Enums;

enum StockMovementType: string
{
    case IN = 'in';
    case OUT = 'out';
    case ADJUSTMENT = 'adjustment';
    case RETURN = 'return';

    public function label(): string
    {
        return match ($this) {
            self::IN => 'Masuk',
            self::OUT => 'Keluar',
            self::ADJUSTMENT => 'Penyesuaian',
            self::RETURN => 'Retur',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::IN => 'success',
            self::OUT => 'danger',
            self::ADJUSTMENT => 'warning',
            self::RETURN => 'info',
        };
    }

    /**
     * Apakah tipe ini menambah stok (+)?
     */
    public function isAddition(): bool
    {
        return in_array($this, [self::IN, self::RETURN], true);
    }
}