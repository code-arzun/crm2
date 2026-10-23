<div>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('admin.orders') }}" class="text-indigo-600 hover:underline text-sm font-medium">&larr; Kembali ke Daftar</a>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white mt-2">Detail Pesanan {{ $order->kode_order }}</h1>
        </div>
        <div>
            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-bold">{{ $order->status->label() }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Info Customer -->
        <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Informasi Customer</h3>
            <p class="text-sm"><strong>Nama:</strong> {{ $order->customer->nama_lengkap }}</p>
            <p class="text-sm"><strong>Perusahaan:</strong> {{ $order->customer->nama_perusahaan ?? '-' }}</p>
            <p class="text-sm"><strong>Telepon:</strong> {{ $order->customer->telepon }}</p>
            <p class="text-sm"><strong>Alamat:</strong> {{ $order->customer->alamat ?? '-' }}</p>
        </div>

        <!-- Info Pembayaran -->
        <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Informasi Pembayaran</h3>
            <p class="text-sm"><strong>Total Tagihan:</strong> Rp {{ number_format($order->total, 0, ',', '.') }}</p>
            <p class="text-sm"><strong>Status:</strong> 
                <span class="{{ $order->payment_status->value === 'paid' ? 'text-green-600' : 'text-amber-600' }}">
                    {{ $order->payment_status->label() }}
                </span>
            </p>
            <p class="text-sm"><strong>Sisa Tagihan:</strong> Rp {{ number_format($order->sisaTagihan(), 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Rincian Barang -->
    <div class="mt-6 bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Rincian Barang</h3>
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-700 text-sm text-zinc-500">
                    <th class="py-2">Produk</th>
                    <th class="py-2">Qty Pesan</th>
                    <th class="py-2">Qty Terkirim</th>
                    <th class="py-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr class="border-b border-zinc-100 dark:border-zinc-700/50">
                        <td class="py-3 text-sm">{{ $item->nama_produk }}</td>
                        <td class="py-3 text-sm">{{ $item->qty }}</td>
                        <td class="py-3 text-sm text-indigo-600 font-bold">{{ $item->qty_terkirim }}</td>
                        <td class="py-3 text-right text-sm font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Riwayat Pembayaran -->
    <div class="mt-6 bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Riwayat Pembayaran</h3>
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-700 text-sm text-zinc-500">
                    <th class="py-2">Tanggal</th>
                    <th class="py-2">Nominal</th>
                    <th class="py-2">Bukti</th>
                    <th class="py-2">Status</th>
                    <th class="py-2">Verifikator</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->payments as $payment)
                    <tr class="border-b border-zinc-100 dark:border-zinc-700/50">
                        <td class="py-3 text-sm">{{ $payment->created_at->format('d M Y') }}</td>
                        <td class="py-3 text-sm font-medium">Rp {{ number_format($payment->jumlah, 0, ',', '.') }}</td>
                        <td class="py-3 text-sm">
                            <a href="{{ asset('storage/'.$payment->bukti_transfer) }}" target="_blank" class="text-indigo-600 underline">Lihat</a>
                        </td>
                        <td class="py-3 text-sm">
                            @if($payment->status->value === 'pending')
                                <span class="text-amber-600">Pending</span>
                            @elseif($payment->status->value === 'verified')
                                <span class="text-green-600">Verified</span>
                            @else
                                <span class="text-red-600">Rejected</span>
                            @endif
                        </td>
                        <td class="py-3 text-sm">{{ $payment->verifier->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-4 text-center text-sm text-zinc-500">Belum ada pembayaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>