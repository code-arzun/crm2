<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->invoice->nomor_invoice }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; }
        .header { width: 100%; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; margin-bottom: 30px; }
        .header td { vertical-align: top; }
        .company-name { font-size: 24px; font-weight: bold; color: #4f46e5; margin: 0; }
        .invoice-title { font-size: 28px; font-weight: bold; text-align: right; color: #333; margin: 0; }
        .details { width: 100%; margin-bottom: 30px; }
        .details td { vertical-align: top; }
        .table-items { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table-items th { background-color: #f3f4f6; padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .table-items td { padding: 10px; border-bottom: 1px solid #eee; }
        .table-totals { width: 40%; float: right; border-collapse: collapse; }
        .table-totals td { padding: 8px 10px; }
        .table-totals .label { text-align: right; color: #666; }
        .table-totals .value { text-align: right; font-weight: bold; }
        .table-totals .grand-total { font-size: 18px; color: #4f46e5; border-top: 2px solid #ddd; }
        .footer { clear: both; margin-top: 50px; text-align: center; font-size: 12px; color: #888; }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td>
                <h1 class="company-name">{{ \App\Models\Setting::get('company_name', 'Nama Perusahaan') }}</h1>
                <p>{{ \App\Models\Setting::get('company_address', 'Alamat Perusahaan') }}</p>
                <p>Telp: {{ \App\Models\Setting::get('company_phone', '-') }} | Email: {{ \App\Models\Setting::get('company_email', '-') }}</p>
            </td>
            <td>
                <h2 class="invoice-title">INVOICE</h2>
                <p style="text-align: right; margin: 5px 0;">
                    <strong>No:</strong> {{ $order->invoice->nomor_invoice }}<br>
                    <strong>Tanggal:</strong> {{ $order->invoice->issued_at->format('d F Y') }}
                </p>
            </td>
        </tr>
    </table>

    <table class="details">
        <tr>
            <td width="50%">
                <strong>Ditagihkan Kepada:</strong><br>
                {{ $order->customer->nama_lengkap }}<br>
                {{ $order->customer->nama_perusahaan ?? '' }}<br>
                {{ $order->customer->alamat }}<br>
                Telp: {{ $order->customer->telepon }}
            </td>
            <td width="50%" style="text-align: right;">
                <strong>Status Pembayaran:</strong><br>
                @if($order->payment_status->value === 'paid')
                    <span style="color: green; font-weight: bold;">LUNAS</span>
                @elseif($order->payment_status->value === 'partial')
                    <span style="color: orange; font-weight: bold;">DIBAYAR SEBAGIAN</span>
                @else
                    <span style="color: red; font-weight: bold;">BELUM DIBAYAR</span>
                @endif
                <br><br>
                <strong>No. Pesanan:</strong> {{ $order->kode_order }}
            </td>
        </tr>
    </table>

    <table class="table-items">
        <thead>
            <tr>
                <th>Deskripsi Produk / Jasa</th>
                <th>Harga</th>
                <th>Qty</th>
                <th style="text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->nama_produk }}</td>
                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td>{{ $item->qty }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="table-totals">
        <tr>
            <td class="label">Subtotal:</td>
            <td class="value">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Diskon:</td>
            <td class="value">Rp {{ number_format($order->diskon, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">PPN:</td>
            <td class="value">Rp {{ number_format($order->ppn, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label grand-total">Total Tagihan:</td>
            <td class="value grand-total">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer">
        Terima kasih atas kepercayaan Anda kepada {{ \App\Models\Setting::get('company_name') }}.<br>
        Jika ada pertanyaan mengenai invoice ini, silakan hubungi kami.
    </div>

</body>
</html>