<?php

namespace App\Enums;

enum LeadStatus: string
{
    case BARU = 'baru';
    case DIHUBUNGI = 'dihubungi';
    case NEGOSIASI = 'negosiasi';
    case WON = 'won';
    case LOST = 'lost';

    public function label(): string
    {
        return match ($this) {
            self::BARU => 'Baru',
            self::DIHUBUNGI => 'Dihubungi',
            self::NEGOSIASI => 'Negosiasi',
            self::WON => 'Deal',
            self::LOST => 'Batal',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::BARU => 'secondary',
            self::DIHUBUNGI => 'info',
            self::NEGOSIASI => 'warning',
            self::WON => 'success',
            self::LOST => 'danger',
        };
    }

    /**
     * Apakah status ini menandakan lead selesai (won/lost)?
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::WON, self::LOST], true);
    }
}