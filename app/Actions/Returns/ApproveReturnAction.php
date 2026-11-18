<?php

namespace App\Actions\Returns;

use App\Models\ReturnOrder;
use App\Models\User;

class ApproveReturnAction
{
    public function execute(ReturnOrder $return, User $approver, array $data)
    {
        // 1. Hitung nominal refund dari semua item yang diretur
        $nilaiBarang = 0;
        foreach ($return->items as $retItem) {
            $nilaiBarang += ($retItem->orderItem->harga * $retItem->qty);
        }

        $biayaRetur = (float) ($data['biaya_retur'] ?? 0);
        $nominalRefund = max(0, $nilaiBarang - $biayaRetur);

        // 2. Update status retur menjadi 'disetujui'
        $return->update([
            'status' => 'disetujui',
            'approved_by' => $approver->id,
            'refund_amount' => $nominalRefund,
            'refund_method' => $data['refund_method'],
            'biaya_retur' => $biayaRetur,
        ]);

        // 3. Proses Refund
        if ($data['refund_method'] === 'saldo') {
            $return->order->customer->increment('credit_balance', $nominalRefund);
        } else {
            // Jika transfer, simpan bukti (file sudah di-handle di Livewire)
            if (!empty($data['refund_proof_path'])) {
                $return->update(['refund_proof' => $data['refund_proof_path']]);
            }
        }

        return $return;
    }
}