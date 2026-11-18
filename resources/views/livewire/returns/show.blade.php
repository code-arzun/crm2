<div>
    <!-- HEADER -->
    <div class="mb-6 bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <div class="flex justify-between items-start">
            <div>
                <!-- Tombol Kembali Dinamis -->
                @if(auth()->user()->hasRole('customer'))
                    <a href="{{ route('orders.show', $return->order_id) }}" class="text-indigo-600 hover:underline text-sm font-medium mb-2 inline-block">&larr; Kembali ke Detail Pesanan</a>
                @else
                    <a href="{{ route('returns.index') }}" class="text-indigo-600 hover:underline text-sm font-medium mb-2 inline-block">&larr; Kembali ke Manajemen Retur</a>
                @endif

                <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Detail Pengajuan Retur</div>
                <h1 class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">
                    {{ $return->kode_retur }}
                </h1>
                <div class="text-sm text-zinc-500">
                    Diajukan pada: <strong>{{ $return->created_at->format('d M Y H:i') }}</strong>
                </div>
            </div>
            <div>
                <!-- Badge Status -->
                @if($return->status->value === 'diajukan')
                    <span class="px-4 py-2 bg-amber-100 text-amber-800 rounded-lg text-sm font-bold shadow-sm border border-amber-200">Menunggu Persetujuan</span>
                @elseif($return->status->value === 'disetujui')
                    <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-lg text-sm font-bold shadow-sm border border-blue-200">Disetujui (Menunggu Diterima)</span>
                @elseif($return->status->value === 'selesai')
                    <span class="px-4 py-2 bg-green-100 text-green-800 rounded-lg text-sm font-bold shadow-sm border border-green-200">Selesai</span>
                @else
                    <span class="px-4 py-2 bg-red-100 text-red-800 rounded-lg text-sm font-bold shadow-sm border border-red-200">Ditolak</span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- KIRI: Info Pesanan & Customer -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2">Rincian Barang Retur</h3>
                
                <table class="w-full text-left mb-6">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700 text-sm text-zinc-500">
                            <th class="py-2">Produk</th>
                            <th class="py-2 text-center">Qty Diretur</th>
                            <th class="py-2 text-right">Nilai Barang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($return->items as $item)
                            <tr>
                                <td class="py-3 text-sm text-zinc-900 dark:text-white">
                                    {{ $item->orderItem->product->nama ?? 'Produk Dihapus' }}
                                </td>
                                <td class="py-3 text-center font-bold text-zinc-900 dark:text-white">
                                    {{ $item->qty }}
                                </td>
                                <td class="py-3 text-right font-medium text-zinc-900 dark:text-white">
                                    Rp {{ number_format($item->orderItem->harga * $item->qty, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs text-zinc-500 mb-1">Kategori Alasan</div>
                        <div class="text-sm font-medium text-zinc-900 dark:text-white capitalize">{{ str_replace('_', ' ', $return->alasan_retur) }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-zinc-500 mb-1">No. Pesanan Asal</div>
                        <a href="{{ route('admin.orders.show', $return->order_id) }}" class="text-sm font-medium text-indigo-600 hover:underline">{{ $return->order->kode_order }}</a>
                    </div>
                    <div class="col-span-2">
                        <div class="text-xs text-zinc-500 mb-1">Deskripsi / Catatan dari Customer</div>
                        <div class="text-sm text-zinc-700 dark:text-zinc-300 bg-zinc-50 dark:bg-zinc-900/50 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700">{{ $return->alasan ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KANAN: Info Refund & Customer -->
        <div class="space-y-6">
            <!-- Info Customer -->
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2">Informasi Customer</h3>
                <div class="grid grid-cols-2 gap-y-2">
                    <div class="text-xs text-zinc-500">Nama</div>
                    <div class="text-sm font-medium text-right">{{ $return->order->customer->nama_lengkap }}</div>
                    <div class="text-xs text-zinc-500">Perusahaan</div>
                    <div class="text-sm font-medium text-right">{{ $return->order->customer->nama_perusahaan ?? '-' }}</div>
                </div>
            </div>

            <!-- Info Refund (Hanya muncul jika disetujui/selesai) -->
            @if(in_array($return->status->value, ['disetujui', 'selesai']))
                <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                    <h3 class="font-bold text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2">Informasi Refund</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-500">Biaya Kirim Retur</span>
                            <span class="text-sm font-medium text-red-600">- Rp {{ number_format($return->biaya_retur, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-zinc-100 dark:border-zinc-700">
                            <span class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Nominal Refund</span>
                            <span class="text-lg font-bold text-green-600">Rp {{ number_format($return->refund_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-700">
                        <div class="text-xs text-zinc-500 mb-1">Metode Refund</div>
                        <div class="text-sm font-bold text-zinc-900 dark:text-white capitalize mb-3">{{ $return->refund_method === 'saldo' ? 'Saldo Akun (Kredit)' : 'Transfer Manual' }}</div>
                        
                        @if($return->refund_method === 'transfer' && $return->refund_proof)
                            <div class="text-xs text-zinc-500 mb-1">Bukti Transfer</div>
                            <a href="{{ asset('storage/'.$return->refund_proof) }}" target="_blank" class="inline-block bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded text-xs font-bold border border-indigo-200 hover:bg-indigo-100">Lihat Bukti</a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>