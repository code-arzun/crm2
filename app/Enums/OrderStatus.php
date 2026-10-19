<?php
namespace App\Enums;

enum OrderStatus: string {
    case DRAFT = 'draft';
    case MENUNGGU_APPROVAL = 'menunggu_approval';
    case DIKONFIRMASI = 'dikonfirmasi';
    case DIPROSES = 'diproses';
    case DIKIRIM = 'dikirim';
    case SELESAI = 'selesai';
    case BATAL = 'batal';

    public function label(): string {
        return match($this) {
            self::DRAFT => 'Draft',
            self::MENUNGGU_APPROVAL => 'Menunggu Approval',
            self::DIKONFIRMASI => 'Dikonfirmasi',
            self::DIPROSES => 'Diproses',
            self::DIKIRIM => 'Dikirim',
            self::SELESAI => 'Selesai',
            self::BATAL => 'Batal',
        };
    }

    public function badge(): string {
        return match($this) {
            self::DRAFT => 'secondary',
            self::MENUNGGU_APPROVAL => 'warning',
            self::DIKONFIRMASI => 'info',
            self::DIPROSES => 'primary',
            self::DIKIRIM => 'primary',
            self::SELESAI => 'success',
            self::BATAL => 'danger',
        };
    }
}