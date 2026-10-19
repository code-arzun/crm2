<?php

namespace App\Enums;

enum OrderChannel: string
{
    case SELF_ORDER = 'self_order';
    case SALES_ORDER = 'sales_order';

    public function label(): string
    {
        return match ($this) {
            self::SELF_ORDER => 'Order Mandiri (Customer)',
            self::SALES_ORDER => 'Order via Sales',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::SELF_ORDER => 'info',
            self::SALES_ORDER => 'primary',
        };
    }

    /**
     * Apakah order lewat jalur ini butuh approval?
     */
    public function requiresApproval(): bool
    {
        return $this === self::SALES_ORDER;
    }
}