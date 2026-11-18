<?php

namespace App\Actions\Shipping;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\StockMovement;
use Exception;

class ProcessShipmentAction
{
    /**
     * Eksekusi proses pengiriman.
     *
     * @param Order $order
     * @param array $data ['ekspedisi' => string, 'no_resi' => string, 'qty_kirim' => array]
     * @param int $userId
     * @return Shipment
     * @throws Exception
     */
    public function execute(Order $order, array $data, int $userId): Shipment
    {
        // 1. Buat data Shipment (Header)
        $shipment = Shipment::create([
            'order_id' => $order->id,
            'user_id' => $userId,
            'ekspedisi' => $data['ekspedisi'],
            'no_resi' => $data['no_resi'],
            'alamat_kirim' => $order->customer->alamat ?? 'Alamat belum diisi',
            'status' => 'dikirim',
            'shipped_at' => now(),
        ]);

        $adaYangDikirim = false;

        // 2. Loop item yang dikirim
        foreach ($order->items as $item) {
            $qtyKirim = (int) ($data['qty_kirim'][$item->id] ?? 0);
            
            if ($qtyKirim > 0) {
                $adaYangDikirim = true;
                
                // Catat di shipment_items
                $shipment->items()->create([
                    'order_item_id' => $item->id,
                    'qty_dikirim' => $qtyKirim,
                ]);

                // Update qty_terkirim di order_items
                $item->increment('qty_terkirim', $qtyKirim);

                // POTONG STOK FISIK
                $product = $item->product;
                if ($product && $product->tipe->value === 'barang') {
                    $product->decrement('stok', $qtyKirim);

                    // Catat Mutasi Stok
                    StockMovement::create([
                        'product_id' => $product->id,
                        'tipe' => 'out',
                        'qty' => $qtyKirim,
                        'referensi' => $order->kode_order,
                        'keterangan' => 'Pengiriman barang (Resi: ' . $data['no_resi'] . ')',
                        'user_id' => $userId,
                    ]);
                }
            }
        }

        if (!$adaYangDikirim) {
            // Jika tidak ada yang dikirim, hapus shipment yang baru dibuat dan lempar error
            $shipment->delete();
            throw new Exception('Tidak ada barang yang dipilih untuk dikirim.');
        }

        // 3. Update Status Order
        $order->refresh();
        $semuaTerkirim = $order->items->every(fn($i) => $i->qty_terkirim >= $i->qty);
        
        if ($semuaTerkirim) {
            $order->update(['status' => 'dikirim']);
        } else {
            $order->update(['status' => 'diproses']); 
        }

        $order->refresh()->cekSelesai();

        return $shipment;
    }
}