<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Pesanan Saya</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Riwayat transaksi pembelian Anda.</p>
        </div>
    </div>

        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700 text-sm font-medium text-zinc-500 dark:text-zinc-400">
                        <th class="px-6 py-4">No. Order & Tanggal</th>
                        <th class="px-6 py-4">Total Belanja</th>
                        <th class="px-6 py-4">Status Pesanan</th>
                        <th class="px-6 py-4">Pembayaran</th>
                        <th class="px-6 py-4">Pengiriman</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($orders as $order)
                        <!-- ROW BISA DIKLIK KE HALAMAN DETAIL -->
                        <tr onclick="window.location='{{ route('orders.show', $order->id) }}'" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition cursor-pointer">
                            
                            <!-- 1. No Order & Tanggal -->
                            <td class="px-6 py-4">
                                <div class="font-mono font-medium text-indigo-600 dark:text-indigo-400">{{ $order->kode_order }}</div>
                                <div class="text-xs text-zinc-500">{{ $order->created_at->format('d M Y') }}</div>
                            </td>
                            
                            <!-- 2. Total -->
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>

                            <!-- 3. Status Pesanan -->
                            <td class="px-6 py-4">
                                @if($order->status->value === 'menunggu_approval')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-amber-100 text-amber-800 rounded-full">Menunggu Persetujuan</span>
                                @elseif($order->status->value === 'dikonfirmasi')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-blue-100 text-blue-800 rounded-full">Disetujui</span>
                                @elseif($order->status->value === 'diproses')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-purple-100 text-purple-800 rounded-full">Diproses</span>
                                @elseif($order->status->value === 'dikirim')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-indigo-100 text-indigo-800 rounded-full">Dikirim</span>
                                @elseif($order->status->value === 'selesai')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full">Selesai</span>
                                @elseif($order->status->value === 'batal')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-red-100 text-red-800 rounded-full">Dibatalkan</span>
                                @endif
                            </td>

                            <!-- 4. Status Pembayaran -->
                            <td class="px-6 py-4">
                                @if($order->status->value === 'menunggu_approval' || $order->status->value === 'batal')
                                    <span class="text-zinc-400">-</span>
                                @else
                                    @if($order->payment_status->value === 'paid')
                                        <span class="text-green-600 font-medium">Lunas</span>
                                    @elseif($order->payment_status->value === 'partial')
                                        <span class="text-amber-600 font-medium">Sebagian / DP</span>
                                    @else
                                        <span class="text-red-600 font-medium">Belum Bayar</span>
                                    @endif
                                @endif
                            </td>

                            <!-- 5. Status Pengiriman (Logika Cerdas) -->
                            <td class="px-6 py-4">
                                @if($order->status->value === 'menunggu_approval' || $order->status->value === 'batal' || $order->payment_status->value === 'unpaid')
                                    <span class="text-zinc-400">-</span>
                                @else
                                    @php
                                        $totalPesan = $order->items->sum('qty');
                                        $totalKirim = $order->items->sum('qty_terkirim');
                                    @endphp
                                    
                                    @if($totalKirim == 0)
                                        <span class="text-zinc-500">Belum Dikirim</span>
                                    @elseif($totalKirim < $totalPesan)
                                        <span class="text-indigo-600 font-medium">Dikirim Sebagian</span>
                                    @else
                                        <span class="text-green-600 font-medium">Terkirim Semua</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">
                                Anda belum memiliki riwayat pesanan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">{{ $orders->links() }}</div>
        @endif
    </div>
</div>