<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Riwayat Mutasi Stok</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Audit trail pergerakan barang masuk dan keluar.</p>
        </div>
    </div>

    <!-- NAVIGASI TAB -->
    <div class="flex border-b border-zinc-200 dark:border-zinc-700 mb-6">
        <button 
            wire:click="setTab('masuk')" 
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'masuk' ? 'border-green-600 text-green-600 dark:text-green-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}"
        >
            Barang Masuk
        </button>
        <button 
            wire:click="setTab('keluar')" 
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'keluar' ? 'border-red-600 text-red-600 dark:text-red-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}"
        >
            Barang Keluar
        </button>
    </div>

    <!-- TOOLBAR FILTER -->
    <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            
            <!-- Filter Kategori (BARU) -->
            <div>
                <label class="block text-xs font-medium text-zinc-500 mb-1">Kategori</label>
                <select wire:model.live="filterCategory" class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Produk -->
            <div>
                <label class="block text-xs font-medium text-zinc-500 mb-1">Pilih Produk Spesifik</label>
                <select wire:model.live="filterProduct" class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                    <option value="">Semua Produk</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->kode }} - {{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Periode -->
            <div>
                <label class="block text-xs font-medium text-zinc-500 mb-1">Periode</label>
                <select wire:model.live="filterPeriode" class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                    <option value="semua">Semua Waktu</option>
                    <option value="hari_ini">Hari Ini</option>
                    <option value="bulan_ini">Bulan Ini</option>
                    <option value="tahun_ini">Tahun Ini</option>
                </select>
            </div>

            <div class="flex justify-end">
                <button wire:click="$set('filterCategory', ''); $set('filterProduct', ''); $set('filterPeriode', 'semua'); $set('search', '');" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    <!-- TABEL -->
    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700 text-sm font-medium text-zinc-500 dark:text-zinc-400">
                        <th class="px-6 py-4">Tanggal & Waktu</th>
                        <th class="px-6 py-4">Produk</th>
                        <th class="px-6 py-4 text-center">Tipe</th>
                        <th class="px-6 py-4 text-center">Qty</th>
                        <th class="px-6 py-4">Referensi</th>
                        <th class="px-6 py-4">Keterangan</th>
                        <th class="px-6 py-4">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition">
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400 whitespace-nowrap">
                                {{ $log->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $log->product->nama ?? 'Produk Dihapus' }}</div>
                                <div class="text-xs text-zinc-500 font-mono">{{ $log->product->kode ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($log->tipe->value === 'in')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full border border-green-200">Masuk</span>
                                @elseif($log->tipe->value === 'out')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-red-100 text-red-800 rounded-full border border-red-200">Keluar</span>
                                @elseif($log->tipe->value === 'adjustment')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-amber-100 text-amber-800 rounded-full border border-amber-200">Adjust</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold bg-blue-100 text-blue-800 rounded-full border border-blue-200">Retur</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-zinc-900 dark:text-white">
                                {{ $log->tipe->isAddition() ? '+' : '-' }}{{ $log->qty }}
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-indigo-600 dark:text-indigo-400">
                                {{ $log->referensi ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $log->keterangan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-white">
                                {{ $log->user->name ?? 'Sistem' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">
                                Belum ada riwayat barang {{ $activeTab }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">{{ $logs->links() }}</div>
        @endif
    </div>
</div>