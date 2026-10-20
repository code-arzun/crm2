<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan {{ $shipment->no_resi ?? $shipment->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; }
        .header { width: 100%; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; margin-bottom: 30px; }
        .header td { vertical-align: top; }
        .company-name { font-size: 24px; font-weight: bold; color: #4f46e5; margin: 0; }
        .doc-title { font-size: 28px; font-weight: bold; text-align: right; color: #333; margin: 0; }
        .details { width: 100%; margin-bottom: 30px; }
        .details td { vertical-align: top; }
        .table-items { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table-items th { background-color: #f3f4f6; padding: 10px; text-align: left; border-bottom: 1px solid #ddd; border-top: 1px solid #ddd; }
        .table-items td { padding: 10px; border-bottom: 1px solid #eee; }
        .footer { margin-top: 50px; width: 100%; }
        .footer td { text-align: center; vertical-align: bottom; height: 80px; }
        .signature-line { border-top: 1px solid #333; width: 80%; margin: 0 auto; padding-top: 5px; }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td>
                <h1 class="company-name">{{ \App\Models\Setting::get('company_name', 'Nama Perusahaan') }}</h1>
                <p>{{ \App\Models\Setting::get('company_address', 'Alamat Perusahaan') }}</p>
                <p>Telp: {{ \App\Models\Setting::get('company_phone', '-') }}</p>
            </td>
            <td>
                <h2 class="doc-title">SURAT JALAN</h2>
                <p style="text-align: right; margin: 5px 0;">
                    <strong>No. Pengiriman:</strong> SHP-{{ str_pad($shipment->id, 5, '0', STR_PAD_LEFT) }}<br>
                    <strong>Tanggal:</strong> {{ $shipment->created_at->format('d F Y') }}<br>
                    <strong>Ekspedisi:</strong> {{ $shipment->ekspedisi }}<br>
                    <strong>No. Resi:</strong> {{ $shipment->no_resi ?? '-' }}
                </p>
            </td>
        </tr>
    </table>

    <table class="details">
        <tr>
            <td width="50%">
                <strong>Kepada (Penerima):</strong><br>
                {{ $shipment->order->customer->nama_lengkap }}<br>
                {{ $shipment->order->customer->nama_perusahaan ?? '' }}<br>
                {{ $shipment->order->customer->telepon }}<br><br>
                <strong>Alamat Pengiriman:</strong><br>
                {{ $shipment->alamat_kirim }}
            </td>
            <td width="50%" style="text-align: right;">
                <strong>Ref. Pesanan:</strong> {{ $shipment->order->kode_order }}<br>
                <strong>Status:</strong> {{ strtoupper($shipment->status->value) }}
            </td>
        </tr>
    </table>

    <table class="table-items">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode Produk</th>
                <th>Nama Barang / Jasa</th>
                <th width="15%" style="text-align: center;">Qty Kirim</th>
                <th width="15%" style="text-align: right;">Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shipment->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->orderItem->product->kode ?? '-' }}</td>
                    <td>{{ $item->orderItem->nama_produk }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $item->qty_dikirim }}</td>
                    <td style="text-align: right;">{{ $item->orderItem->product->satuan ?? 'pcs' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-size: 12px; color: #666; font-style: italic;">
        Catatan: Barang yang sudah diterima dianggap sesuai dengan Surat Jalan ini. Mohon periksa barang sebelum menandatangani.
    </p>

    <table class="footer">
        <tr>
            <td>
                <div class="signature-line">Dibuat Oleh (Gudang)</div>
            </td>
            <td>
                <div class="signature-line">Kurir / Ekspedisi</div>
            </td>
            <td>
                <div class="signature-line">Diterima Oleh (Customer)</div>
            </td>
        </tr>
    </table>

</body>
</html>