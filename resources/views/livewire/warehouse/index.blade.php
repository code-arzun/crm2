<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Stok Gudang</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Pantau sisa stok dan lakukan penambahan barang (restock).</p>
        </div>
    </div>

       <!-- Toolbar -->
    <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm mb-6 flex justify-between items-center">
        <div class="w-full md:w-1/3">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau kode barang..." class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
        </div>
        
        <!-- TOMBOL SIMPAN MASSAL -->
        <button wire:click="saveBulkStock" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg text-sm font-bold transition shadow-sm">
            Simpan Stok Massal
        </button>
    </div>

    <!-- Tabel -->
    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700 text-sm font-medium text-zinc-500 dark:text-zinc-400">
                        <th class="px-6 py-4">Kode & Nama Barang</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4 text-center">Sisa Stok</th>
                        <th class="px-6 py-4 text-center">Tambah Stok (Qty)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($products as $product)
                        <tr class="hover:bg-indigo-50/50 dark:hover:bg-zinc-700/50 transition">
                            <td class="px-6 py-4">
                                <div class="text-xs text-zinc-500 font-mono">{{ $product->kode }}</div>
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $product->nama }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $product->category ? $product->category->nama : '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 text-sm font-bold rounded-full border 
                                    {{ $product->stok > 10 ? 'bg-green-100 text-green-800 border-green-200' : '' }}
                                    {{ $product->stok > 0 && $product->stok <= 10 ? 'bg-amber-100 text-amber-800 border-amber-200' : '' }}
                                    {{ $product->stok == 0 ? 'bg-red-100 text-red-800 border-red-200' : '' }}
                                ">
                                    {{ $product->stok }} {{ $product->satuan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <!-- INPUT BULK -->
                                <input type="number" wire:model="bulk_qty.{{ $product->id }}" min="1" placeholder="0" class="w-24 px-3 py-2 text-center border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">Tidak ada data barang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">{{ $products->links() }}</div>
        @endif
    </div>