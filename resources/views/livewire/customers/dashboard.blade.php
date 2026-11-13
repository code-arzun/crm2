<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Halo, {{ auth()->user()->name }}!</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Berikut adalah ringkasan aktivitas akun Anda.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Kartu Tagihan -->
        <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-zinc-500">Total Tagihan Aktif</p>
                <p class="text-2xl font-bold text-red-600 mt-1">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Kartu Pengiriman -->
        <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-zinc-500">Paket Dalam Pengiriman</p>
                <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">{{ $shippingOrders->count() }} Paket</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
            </div>
        </div>
    </div>

    <!-- Tabel Pesanan Aktif -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
            <h3 class="font-bold text-zinc-900 dark:text-white">Pesanan Aktif</h3>
            <a href="{{ route('orders.my') }}" class="text-sm text-indigo-600 hover:underline">Lihat Semua</a>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700 text-sm text-zinc-500">
                    <th class="px-6 py-3">No. Order</th>
                    <th class="px-6 py-3">Tanggal</th>
                    <th class="px-6 py-3">Total</th>
                    <th class="px-6 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($activeOrders as $order)
                    <tr onclick="window.location='{{ route('orders.show', $order->id) }}'" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 cursor-pointer transition">
                        <td class="px-6 py-3 font-mono text-indigo-600 text-sm">{{ $order->kode_order }}</td>
                        <td class="px-6 py-3 text-sm">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-3 text-sm font-medium">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 text-xs font-bold bg-blue-100 text-blue-800 rounded-full">{{ $order->status->label() }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-zinc-500">Tidak ada pesanan aktif.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Tombol CTA Belanja -->
    <div class="text-center">
        <a href="{{ route('catalog.index') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-full font-bold shadow-lg transition">
            Mulai Belanja Lagi &rarr;
        </a>
    </div>
</div>