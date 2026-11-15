<!-- KOP SURAT -->
<div class="flex justify-between items-start border-b-2 border-indigo-600 pb-6 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-indigo-600 mb-2">{{ \App\Models\Setting::get('company_name', 'Nama Perusahaan') }}</h1>
        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ \App\Models\Setting::get('company_address', 'Alamat Perusahaan') }}</p>
        <p class="text-sm text-zinc-600 dark:text-zinc-400">Telp: {{ \App\Models\Setting::get('company_phone', '-') }}</p>
    </div>
    <div class="text-right">
        <h2 class="text-3xl font-bold text-zinc-900 dark:text-white mb-2">INVOICE</h2>
        <p class="text-sm"><strong>No:</strong> {{ $order->invoice->nomor_invoice ?? '-' }}</p>
        <p class="text-sm"><strong>Tanggal:</strong> {{ $order->invoice ? $order->invoice->issued_at->format('d F Y') : '-' }}</p>
    </div>
</div>

<!-- INFO CUSTOMER -->
<div class="flex justify-between mb-8">
    <div>
        <p class="text-sm text-zinc-500 mb-1">Ditagihkan Kepada:</p>
        <p class="font-bold">{{ $order->customer->nama_lengkap }}</p>
        <p class="text-sm">{{ $order->customer->nama_perusahaan ?? '' }}</p>
        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $order->customer->alamat }}</p>
    </div>
    <div class="text-right">
        <p class="text-sm text-zinc-500 mb-1">Status Pembayaran:</p>
        @if($order->payment_status->value === 'paid')
            <span class="font-bold text-green-600 uppercase">LUNAS</span>
        @elseif($order->payment_status->value === 'partial')
            <span class="font-bold text-amber-600 uppercase">DIBAYAR SEBAGIAN</span>
        @else
            <span class="font-bold text-red-600 uppercase">BELUM DIBAYAR</span>
        @endif
        <p class="text-sm mt-2"><strong>No. Pesanan:</strong> {{ $order->kode_order }}</p>
    </div>
</div>

<!-- TABEL BARANG -->
<table class="w-full text-left mb-8 border-collapse">
    <thead>
        <tr class="bg-zinc-50 dark:bg-zinc-900 border-y border-zinc-200 dark:border-zinc-700">
            <th class="py-3 px-4">Deskripsi Produk</th>
            <th class="py-3 px-4">Harga</th>
            <th class="py-3 px-4 text-center">Qty</th>
            <th class="py-3 px-4 text-right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
            <tr class="border-b border-zinc-100 dark:border-zinc-700/50">
                <td class="py-3 px-4 text-sm">{{ $item->nama_produk }}</td>
                <td class="py-3 px-4 text-sm">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="py-3 px-4 text-sm text-center">{{ $item->qty }}</td>
                <td class="py-3 px-4 text-sm text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- TOTAL -->
<div class="flex justify-end">
    <div class="w-1/2 space-y-2">
        <div class="flex justify-between text-sm">
            <span class="text-zinc-500">Subtotal</span>
            <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
        </div>
        
        {{-- @if($order->diskon > 0) --}}
            <div class="flex justify-between text-sm">
                <span class="text-zinc-500">Diskon {{ $order->diskon_tipe === 'persen' ? '('.$order->diskon.'%)' : '' }}</span>
                <span>- Rp {{ number_format($order->diskon_tipe === 'persen' ? ($order->subtotal * $order->diskon / 100) : $order->diskon, 0, ',', '.') }}</span>
            </div>
        {{-- @endif --}}

        <div class="flex justify-between text-sm">
            <span class="text-zinc-500">PPN</span>
            <span>Rp {{ number_format($order->ppn, 0, ',', '.') }}</span>
        </div>
        

        <div class="flex justify-between text-sm">
            <span class="text-zinc-500">Biaya Pengiriman</span>
            <span>Rp {{ number_format($order->biaya_pengiriman, 0, ',', '.') }}</span>
        </div>
        
        <div class="flex justify-between font-bold text-lg pt-2 border-t border-zinc-200 dark:border-zinc-700 text-indigo-600">
            <span>Grand Total</span>
            <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
    </div>
</div>

<div class="mt-12 text-center text-xs text-zinc-500">
    Terima kasih atas kepercayaan Anda kepada {{ \App\Models\Setting::get('company_name') }}.
</div>