<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shipment;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download($orderId)
    {
        $order = Order::with(['items', 'customer', 'invoice'])->findOrFail($orderId);

        if (auth()->user()->hasRole('customer') && $order->customer_id !== auth()->user()->customer?->id) {
            abort(403, 'Anda tidak berhak mengunduh invoice ini.');
        }

        if (!$order->invoice) {
            abort(404, 'Invoice belum diterbitkan untuk pesanan ini.');
        }

        $pdf = Pdf::loadView('pdf.invoice', ['order' => $order]);
        $pdf->setPaper('A4', 'portrait');

        // Perbaikan ada di dua baris ini:
        $namaFile = str_replace('/', '-', $order->invoice->nomor_invoice);
        return $pdf->download('Invoice-' . $namaFile . '.pdf');
    }

    public function downloadSuratJalan($shipmentId)
    {
        // Ambil data shipment beserta relasinya
        $shipment = Shipment::with(['order.customer', 'items.orderItem.product'])->findOrFail($shipmentId);

        // Keamanan: Pastikan yang mengunduh adalah Admin/Gudang, atau Customer pemilik pesanan
        if (auth()->user()->hasRole('customer') && $shipment->order->customer_id !== auth()->user()->customer?->id) {
            abort(403);
        }

        $pdf = Pdf::loadView('pdf.surat-jalan', ['shipment' => $shipment]);
        $pdf->setPaper('A4', 'portrait');

        // return $pdf->download('SuratJalan-' . $shipment->order->kode_order . '.pdf');
        $namaFile = str_replace('/', '-', $shipment->order->kode_order);
        return $pdf->download('SuratJalan-' . $namaFile . '-SHP' . $shipment->id . '.pdf');
    }
}