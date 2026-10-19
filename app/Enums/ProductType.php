<?php

namespace App\Enums;

enum ProductType: string
{
    case BARANG = 'barang';
    case JASA = 'jasa';

    public function label(): string
    {
        return match ($this) {
            self::BARANG => 'Barang',
            self::JASA => 'Jasa',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::BARANG => 'primary',
            self::JASA => 'info',
        };
    }

    /**
     * Apakah tipe produk ini mengelola stok?
     */
    public function managesStock(): bool
    {
        return $this === self::BARANG;
    }
}