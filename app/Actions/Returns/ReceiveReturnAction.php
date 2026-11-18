<?php

namespace App\Actions\Returns;

use App\Models\ReturnOrder;
use App\Models\StockMovement;

class ReceiveReturnAction
{
    public function execute(ReturnOrder $return, int $userId)
    {
        if ($return->status->value !== 'disetujui') {
            throw new \Exception('Status retur tidak valid untuk diterima.');
        }

        // 1. Update status retur menjadi selesai
        $return->update(['status' => 'selesai']);

        // 2. Loop semua item dan kembalikan stoknya
        foreach ($return->items as $retItem) {
            $product = $retItem->orderItem->product;
            
            if ($product && $product->tipe->value === 'barang') {
                // Tambah stok
                $product->increment('stok', $retItem->qty);
                
                // Catat Mutasi
                StockMovement::create([
                    'product_id' => $product->id,
                    'tipe' => 'return',
                    'qty' => $retItem->qty,
                    'referensi' => $return->order->kode_order,
                    'keterangan' => 'Retur barang diterima gudang',
                    'user_id' => $userId,
                ]);
            }
        }

        return $return;
    }
}