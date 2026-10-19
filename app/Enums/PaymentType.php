<?php

namespace App\Enums;

enum PaymentType: string
{
    case TRANSFER = 'transfer';
    case KREDIT = 'kredit';
    case MIDTRANS = 'midtrans';
    case XENDIT = 'xendit';
    case STRIPE = 'stripe';

    public function label(): string
    {
        return match ($this) {
            self::TRANSFER => 'Transfer Manual',
            self::KREDIT => 'Kredit / Tempo',
            self::MIDTRANS => 'Midtrans',
            self::XENDIT => 'Xendit',
            self::STRIPE => 'Stripe',
        };
    }

    /**
     * Apakah tipe pembayaran ini butuh verifikasi manual?
     */
    public function needsManualVerification(): bool
    {
        return in_array($this, [self::TRANSFER, self::KREDIT], true);
    }

    /**
     * Apakah tipe pembayaran ini memakai payment gateway online?
     */
    public function isOnlineGateway(): bool
    {
        return in_array($this, [self::MIDTRANS, self::XENDIT, self::STRIPE], true);
    }
}