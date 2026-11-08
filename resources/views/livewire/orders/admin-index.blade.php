<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Kelola Pesanan (Admin)</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Pantau dan setujui pesanan dari customer.</p>
        </div>
    </div>

    <!-- TOOLBAR FILTER & PENCARIAN -->
    <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            
            <!-- Pencarian -->
            <div class="flex-1 w-full">
                <label class="block text-xs font-medium text-zinc-500 mb-1">Pencarian</label>
                <input 
                    wire:model.live.debounce.300ms="search"
                    type="text" 
                    placeholder="Cari No. Order atau Nama Customer..." 
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
            </div>

            <!-- Filter Status -->
            <div class="w-full md:w-48">
                <label class="block text-xs font-medium text-zinc-500 mb-1">Status</label>
                <select wire:model.live="filterStatus" class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="semua">Semua Status</option>
                    <option value="menunggu_approval">Menunggu Approval</option>
                    <option value="dikonfirmasi">Dikonfirmasi</option>
                    <option value="diproses">Diproses</option>
                    <option value="dikirim">Dikirim</option>
                    <option value="selesai">Selesai</option>
                    <option value="batal">Dibatalkan</option>
                </select>
            </div>

            <!-- Filter Periode (Opsi B) -->
            <div class="w-full md:w-48">
                <label class="block text-xs font-medium text-zinc-500 mb-1">Periode</label>
                <select wire:model.live="filterPeriode" class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="semua">Semua Waktu</option>
                    <option value="hari_ini">Hari Ini</option>
                    <option value="bulan_ini">Bulan Ini</option>
                    <option value="tahun_ini">Tahun Ini</option>
                </select>
            </div>
        </div>

        <!-- Indikator Hasil Pencarian -->
        @if($search || $filterStatus !== 'semua' || $filterPeriode !== 'semua')
            <div class="mt-3 text-sm text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 px-3 py-2 rounded-lg flex justify-between items-center">
                <span>Menampilkan {{ $orders->total() }} hasil sesuai filter.</span>
                <button wire:click="$set('search', ''); $set('filterStatus', 'semua'); $set('filterPeriode', 'semua');" class="text-xs font-bold underline hover:text-indigo-800">Reset Filter</button>
            </div>
        @endif
    </div>

    <!-- TABEL -->
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
                        <!-- BARIS BISA DIKLIK & HOVER EFFECT -->
                        <tr onclick="window.location='{{ route('admin.orders.show', $order->id) }}'" class="hover:bg-indigo-50/50 dark:hover:bg-zinc-700/50 transition cursor-pointer">
                            
                            <td class="px-6 py-4">
                                <div class="font-mono font-medium text-indigo-600 dark:text-indigo-400">{{ $order->kode_order }}</div>
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
                            <td class="px-6 py-4 text-right">
                                @role('superadmin|admin|finance')
                                @if($order->status->value === 'menunggu_approval')
                                    <button onclick="event.stopPropagation(); if(confirm('Setujui pesanan ini?')) @this.approve({{ $order->id }})" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm font-bold shadow-sm transition">
                                        Setujui
                                    </button>
                                @endrole
                                @else
                                    <span class="text-xs text-zinc-400 italic">Lihat Detail &rarr;</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">
                                Tidak ada pesanan yang sesuai dengan filter Anda.
                            </td>
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