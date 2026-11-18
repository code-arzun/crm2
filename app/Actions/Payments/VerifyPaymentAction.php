<?php

namespace App\Actions\Payments;

use App\Models\Payment;
use App\Models\User;

class VerifyPaymentAction
{
    public function execute(Payment $payment, User $verifier)
    {
        // 1. Update status payment jadi verified
        $payment->update([
            'status' => 'verified',
            'verified_by' => $verifier->id,
            'verified_at' => now(),
        ]);

        // 2. Sinkronisasi status order (Lunas / Sebagian)
        $payment->order->syncPaymentStatus();

        // 3. Cek apakah order sudah selesai (Lunas + Barang Diterima)
        $payment->order->refresh()->cekSelesai();

        return $payment;
    }
}