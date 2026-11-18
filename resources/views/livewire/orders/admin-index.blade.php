<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Kelola Pesanan (Admin)</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Pantau dan setujui pesanan dari customer.</p>
        </div>
        <!-- Tombol Export Excel -->
        <div x-data="{ openExport: false }" class="relative">
            <button @click="openExport = !openExport" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Excel
            </button>

            <!-- Popup Pilihan Tanggal -->
            <div x-show="openExport" @click.away="openExport = false" style="display: none;" class="absolute right-0 mt-2 w-72 bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-zinc-200 dark:border-zinc-700 p-4 z-50">
                <h4 class="font-bold text-sm mb-3">Filter Tanggal Export</h4>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs text-zinc-500 mb-1">Dari Tanggal</label>
                        <input type="date" wire:model="exportStartDate" class="w-full px-3 py-1.5 text-sm border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-500 mb-1">Sampai Tanggal</label>
                        <input type="date" wire:model="exportEndDate" class="w-full px-3 py-1.5 text-sm border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                    </div>
                    <button wire:click="export" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg text-sm font-bold transition mt-2">
                        Unduh Excel
                    </button>
                </div>
            </div>
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

        <!-- ========================================== -->
    <!-- TAMPILAN DESKTOP (TABEL) -->
    <!-- ========================================== -->
    <div class="hidden md:block bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden shadow-sm">
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
                                <!-- Status Badge -->
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full border 
                                    {{ $order->status->value === 'menunggu_approval' ? 'bg-amber-100 text-amber-800 border-amber-200' : '' }}
                                    {{ $order->status->value === 'dikonfirmasi' ? 'bg-blue-100 text-blue-800 border-blue-200' : '' }}
                                    {{ $order->status->value === 'diproses' ? 'bg-purple-100 text-purple-800 border-purple-200' : '' }}
                                    {{ $order->status->value === 'dikirim' ? 'bg-indigo-100 text-indigo-800 border-indigo-200' : '' }}
                                    {{ $order->status->value === 'selesai' ? 'bg-green-100 text-green-800 border-green-200' : '' }}
                                    {{ $order->status->value === 'batal' ? 'bg-red-100 text-red-800 border-red-200' : '' }}
                                ">
                                    {{ $order->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($order->status->value === 'menunggu_approval')
                                    <button onclick="event.stopPropagation(); if(confirm('Setujui pesanan ini?')) @this.approve({{ $order->id }})" 
                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm font-bold shadow-sm transition min-w-[80px] flex justify-center items-center">
                                        <span wire:loading.remove wire:target="approve({{ $order->id }})">Setujui</span>
                                        <span wire:loading wire:target="approve({{ $order->id }})" class="flex items-center gap-1">
                                            @include('partials.spinner')
                                        </span>
                                    </button>
                                @else
                                    <span class="text-xs text-zinc-400 italic">Lihat Detail &rarr;</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">Tidak ada pesanan yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">{{ $orders->links() }}</div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- TAMPILAN MOBILE (CARD) -->
    <!-- ========================================== -->
    <div class="md:hidden space-y-4">
        @forelse ($orders as $order)
            <div onclick="window.location='{{ route('admin.orders.show', $order->id) }}'" class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-4 shadow-sm cursor-pointer active:bg-zinc-50 dark:active:bg-zinc-700 transition">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $order->kode_order }}</div>
                        <div class="text-xs text-zinc-500">{{ $order->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <!-- Status Badge -->
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full border 
                        {{ $order->status->value === 'menunggu_approval' ? 'bg-amber-100 text-amber-800 border-amber-200' : '' }}
                        {{ $order->status->value === 'dikonfirmasi' ? 'bg-blue-100 text-blue-800 border-blue-200' : '' }}
                        {{ $order->status->value === 'diproses' ? 'bg-purple-100 text-purple-800 border-purple-200' : '' }}
                        {{ $order->status->value === 'dikirim' ? 'bg-indigo-100 text-indigo-800 border-indigo-200' : '' }}
                        {{ $order->status->value === 'selesai' ? 'bg-green-100 text-green-800 border-green-200' : '' }}
                        {{ $order->status->value === 'batal' ? 'bg-red-100 text-red-800 border-red-200' : '' }}
                    ">
                        {{ $order->status->label() }}
                    </span>
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-700/50 pt-2 mt-2">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-zinc-500">Customer:</span>
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $order->customer->nama_lengkap }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-3">
                        <span class="text-zinc-500">Total:</span>
                        <span class="font-bold text-zinc-900 dark:text-white">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($order->status->value === 'menunggu_approval')
                        <button onclick="event.stopPropagation(); if(confirm('Setujui pesanan ini?')) @this.approve({{ $order->id }})" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm font-bold shadow-sm transition flex justify-center items-center">
                            <span wire:loading.remove wire:target="approve({{ $order->id }})">Setujui Pesanan</span>
                            <span wire:loading wire:target="approve({{ $order->id }})" class="flex items-center gap-2">
                                @include('partials.spinner') Memproses...
                            </span>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-zinc-500 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                Tidak ada pesanan yang sesuai.
            </div>
        @endforelse

        <!-- Pagination Mobile -->
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>