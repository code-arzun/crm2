<div>
    <!-- Notifikasi Flash (Jika ada) -->
    @include('partials.flash-message')

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Pesanan Saya</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Riwayat transaksi pembelian Anda.</p>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAMPILAN DESKTOP (TABEL) -->
    <!-- ========================================== -->
    <div class="hidden md:block bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden shadow-sm">
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
                        <tr onclick="window.location='{{ route('orders.show', $order->id) }}'" class="hover:bg-indigo-50/50 transition cursor-pointer">
                            <td class="px-6 py-4">
                                <div class="font-mono font-medium text-indigo-600 dark:text-indigo-400">{{ $order->kode_order }}</div>
                                <div class="text-xs text-zinc-500">{{ $order->created_at->format('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($order->status->value === 'menunggu_approval')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-amber-100 text-amber-800 rounded-full border border-amber-200">Menunggu Persetujuan</span>
                                @elseif($order->status->value === 'dikonfirmasi' || $order->status->value === 'diproses')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-blue-100 text-blue-800 rounded-full border border-blue-200">Disetujui</span>
                                @elseif($order->status->value === 'batal')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-red-100 text-red-800 rounded-full border border-red-200">Dibatalkan</span>
                                @elseif($order->status->value === 'selesai' || $order->status->value === 'dikirim')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full border border-green-200">Selesai</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold bg-zinc-100 text-zinc-800 rounded-full border border-zinc-200">{{ $order->status->label() }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($order->status->value === 'menunggu_approval' || $order->status->value === 'batal')
                                    <span class="text-zinc-400">-</span>
                                @else
                                    @if($order->payment_status->value === 'paid')
                                        <span class="text-green-600 font-bold">Lunas</span>
                                    @elseif($order->payment_status->value === 'partial')
                                        <span class="text-amber-600 font-bold">Sebagian / DP</span>
                                    @else
                                        <span class="text-red-600 font-bold">Belum Bayar</span>
                                    @endif
                                @endif
                            </td>
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
                                        <span class="text-indigo-600 font-bold">Dikirim Sebagian</span>
                                    @else
                                        <span class="text-green-600 font-bold">Terkirim Semua</span>
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

    <!-- ========================================== -->
    <!-- TAMPILAN MOBILE (CARD) -->
    <!-- ========================================== -->
    <div class="md:hidden space-y-4">
        @forelse ($orders as $order)
            <div onclick="window.location='{{ route('orders.show', $order->id) }}'" class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-4 shadow-sm cursor-pointer active:bg-zinc-50 dark:active:bg-zinc-700 transition">
                
                <!-- Header Card -->
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $order->kode_order }}</div>
                        <div class="text-xs text-zinc-500">{{ $order->created_at->format('d M Y') }}</div>
                    </div>
                    <!-- Status Pesanan -->
                    @if($order->status->value === 'menunggu_approval')
                        <span class="px-2 py-1 text-[10px] font-bold bg-amber-100 text-amber-800 rounded-full border border-amber-200">Menunggu</span>
                    @elseif($order->status->value === 'batal')
                        <span class="px-2 py-1 text-[10px] font-bold bg-red-100 text-red-800 rounded-full border border-red-200">Batal</span>
                    @elseif($order->status->value === 'selesai' || $order->status->value === 'dikirim')
                        <span class="px-2 py-1 text-[10px] font-bold bg-green-100 text-green-800 rounded-full border border-green-200">Selesai</span>
                    @else
                        <span class="px-2 py-1 text-[10px] font-bold bg-blue-100 text-blue-800 rounded-full border border-blue-200">Disetujui</span>
                    @endif
                </div>

                <!-- Total -->
                <div class="text-sm font-bold text-zinc-900 dark:text-white mb-3">
                    Rp {{ number_format($order->total, 0, ',', '.') }}
                </div>

                <!-- Status Bawah -->
                <div class="border-t border-zinc-100 dark:border-zinc-700/50 pt-3 flex justify-between text-xs">
                    <div>
                        <span class="text-zinc-500 block">Pembayaran:</span>
                        @if($order->status->value === 'menunggu_approval' || $order->status->value === 'batal')
                            <span class="text-zinc-400">-</span>
                        @elseif($order->payment_status->value === 'paid')
                            <span class="font-bold text-green-600">Lunas</span>
                        @elseif($order->payment_status->value === 'partial')
                            <span class="font-bold text-amber-600">DP / Sebagian</span>
                        @else
                            <span class="font-bold text-red-600">Belum Bayar</span>
                        @endif
                    </div>
                    <div class="text-right">
                        <span class="text-zinc-500 block">Pengiriman:</span>
                        @if($order->status->value === 'menunggu_approval' || $order->status->value === 'batal' || $order->payment_status->value === 'unpaid')
                            <span class="text-zinc-400">-</span>
                        @else
                            @php
                                $totalPesan = $order->items->sum('qty');
                                $totalKirim = $order->items->sum('qty_terkirim');
                            @endphp
                            @if($totalKirim == 0)
                                <span class="font-bold text-zinc-500">Belum Dikirim</span>
                            @elseif($totalKirim < $totalPesan)
                                <span class="font-bold text-indigo-600">Dikirim Sebagian</span>
                            @else
                                <span class="font-bold text-green-600">Terkirim Semua</span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-zinc-500 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                Anda belum memiliki riwayat pesanan.
            </div>
        @endforelse
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>