<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Daftar Produk & Jasa</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Kelola katalog barang dan jasa Anda.</p>
        </div>
        <button wire:click="create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
            + Tambah Produk
        </button>
    </div>

    <!-- Search -->
    <div class="mb-4">
        <input 
            wire:model.live.debounce.300ms="search"
            type="text" 
            placeholder="Cari nama atau kode produk..." 
            class="w-full md:w-1/3 px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
        >
    </div>

    <!-- Tabel -->
    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700 text-sm font-medium text-zinc-500 dark:text-zinc-400">
                        <th class="px-6 py-4">Kode & Nama</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4">Harga Default</th>
                        <th class="px-6 py-4">Stok</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($products as $product)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition">
                            <td class="px-6 py-4">
                                <div class="text-xs text-zinc-500 font-mono">{{ $product->kode }}</div>
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $product->nama }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $product->category ? $product->category->nama : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full 
                                    {{ $product->tipe->value === 'barang' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $product->tipe->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-white">
                                Rp {{ number_format($product->harga_default, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-white">
                                {{ $product->tipe->value === 'jasa' ? '-' : $product->stok . ' ' . $product->satuan }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="edit({{ $product->id }})" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium mr-3">Edit</button>
                                <button x-data x-on:click.prevent="if(confirm('Hapus produk ini?')) $wire.delete({{ $product->id }})" class="text-red-600 hover:text-red-900 text-sm font-medium">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">Tidak ada produk ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">{{ $products->links() }}</div>
        @endif
    </div>

    <!-- MODAL FORM -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">{{ $productId ? 'Edit Produk' : 'Tambah Produk' }}</h3>
                    <button wire:click="closeModal" class="text-zinc-400 hover:text-zinc-600">&times;</button>
                </div>

                <form wire:submit="store" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Kode Produk *</label>
                            <input wire:model="kode" type="text" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                            @error('kode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tipe *</label>
                            <select wire:model="tipe" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                                <option value="barang">Barang</option>
                                <option value="jasa">Jasa</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nama Produk *</label>
                        <input wire:model="nama" type="text" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                        @error('nama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Kategori</label>
                        <select wire:model="category_id" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Harga Default (Rp) *</label>
                            <input wire:model="harga_default" type="number" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                            @error('harga_default') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Stok (Kosongkan jika Jasa)</label>
                            <input wire:model="stok" type="number" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Satuan (pcs, unit, jam, dll) *</label>
                        <input wire:model="satuan" type="text" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                        @error('satuan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-zinc-700 bg-zinc-100 hover:bg-zinc-200 rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>