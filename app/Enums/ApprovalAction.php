<?php

namespace App\Enums;

enum ApprovalAction: string
{
    case APPROVE = 'approve';
    case REJECT = 'reject';

    public function label(): string
    {
        return match ($this) {
            self::APPROVE => 'Disetujui',
            self::REJECT => 'Ditolak',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::APPROVE => 'success',
            self::REJECT => 'danger',
        };
    }
}