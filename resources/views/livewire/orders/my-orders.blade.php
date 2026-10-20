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
                        <th class="px-6 py-4">No. Order</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Total Belanja</th>
                        <th class="px-6 py-4">Pembayaran</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition">
                            <td class="px-6 py-4 font-mono font-medium text-zinc-900 dark:text-white">
                                {{ $order->kode_order }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $order->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($order->payment_status->value === 'paid')
                                    <span class="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Lunas</span>
                                @elseif($order->payment_status->value === 'partial')
                                    <span class="px-2.5 py-1 text-xs font-medium bg-amber-100 text-amber-800 rounded-full">Sebagian</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Belum Bayar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('orders.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Detail</a>
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