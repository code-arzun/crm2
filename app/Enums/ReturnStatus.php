<?php

namespace App\Enums;

enum ReturnStatus: string
{
    case DIAJUKAN = 'diajukan';
    case DISETUJUI = 'disetujui';
    case DITOLAK = 'ditolak';
    case SELESAI = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::DIAJUKAN => 'Diajukan',
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK => 'Ditolak',
            self::SELESAI => 'Selesai',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::DIAJUKAN => 'warning',
            self::DISETUJUI => 'info',
            self::DITOLAK => 'danger',
            self::SELESAI => 'success',
        };
    }
}