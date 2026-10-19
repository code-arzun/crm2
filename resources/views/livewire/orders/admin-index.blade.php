<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Kelola Pesanan (Admin)</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Pantau dan setujui pesanan dari customer.</p>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-4">
        <input 
            wire:model.live.debounce.300ms="search"
            type="text" 
            placeholder="Cari No. Order atau Nama Customer..." 
            class="w-full md:w-1/3 px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
        >
    </div>

    <!-- Tabel -->
    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700 text-sm font-medium text-zinc-500 dark:text-zinc-400">
                        <th class="px-6 py-4">No. Order & Tanggal</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-mono font-medium text-zinc-900 dark:text-white">{{ $order->kode_order }}</div>
                                <div class="text-xs text-zinc-500">{{ $order->created_at->format('d M Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $order->customer->nama_lengkap }}</div>
                                <div class="text-xs text-zinc-500">{{ $order->customer->nama_perusahaan ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($order->status->value === 'menunggu_approval')
                                    <span class="px-2.5 py-1 text-xs font-medium bg-amber-100 text-amber-800 rounded-full">Menunggu Approval</span>
                                @elseif($order->status->value === 'dikonfirmasi')
                                    <span class="px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Dikonfirmasi</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-medium bg-zinc-100 text-zinc-800 rounded-full">{{ $order->status->label() }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($order->status->value === 'menunggu_approval')
                                    <button wire:click="approve({{ $order->id }})" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm font-medium transition shadow-sm">
                                        Setujui
                                    </button>
                                @else
                                    <button class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Detail</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">Belum ada pesanan masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">{{ $orders->links() }}</div>
        @endif
    </div>
</div>