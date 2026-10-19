<?php

namespace App\Actions\Payments;

use App\Models\Payment;
use App\Models\User;

class VerifyPaymentAction
{
    public function execute(Payment $payment, User $user): void
    {
        // $payment->update([...]);
        // $payment->order->syncPaymentStatus();
    }
}