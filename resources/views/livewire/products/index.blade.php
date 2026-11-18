<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Daftar Produk & Jasa</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Kelola katalog barang dan jasa Anda.</p>
        </div>
        <div class="flex gap-2">
    <button wire:click="$set('isImportModalOpen', true)" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
        Import Excel
    </button>
    <button wire:click="create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm">
        + Tambah Produk
    </button>
</div>
        
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
                                <a href="{{ route('products.prices', $product->id) }}" class="text-amber-600 hover:text-amber-900 text-sm font-medium mr-3">Harga Khusus</a>
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4">
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

    <!-- MODAL IMPORT EXCEL -->
    @if($isImportModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Import Produk dari Excel</h3>
                    <button wire:click="$set('isImportModalOpen', false)" class="text-zinc-400 hover:text-zinc-600 text-2xl">&times;</button>
                </div>
                
                <form wire:submit="import" class="p-6 space-y-4">
                    <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                        <p class="text-sm text-blue-800 font-medium mb-2">Langkah-langkah:</p>
                        <ol class="text-xs text-blue-700 list-decimal list-inside space-y-1 mb-3">
                            <li>Unduh template Excel di bawah ini.</li>
                            <li>Isi data produk sesuai kolom.</li>
                            <li>Simpan file, lalu unggah kembali di sini.</li>
                        </ol>
                        <button type="button" wire:click="downloadTemplate" class="w-full bg-white border border-blue-300 text-blue-700 px-3 py-2 rounded text-sm font-bold hover:bg-blue-50 transition flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download Template Excel
                        </button>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Upload File Excel</label>
                        <input type="file" wire:model="file_excel" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" accept=".xlsx, .xls, .csv">
                        @error('file_excel') <span class="text-red-500 text-xs block mt-2">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <button type="button" wire:click="$set('isImportModalOpen', false)" class="px-4 py-2 text-sm font-medium text-zinc-700 bg-zinc-100 hover:bg-zinc-200 rounded-lg">Batal</button>
                        <button type="submit" 
                                wire:loading.attr="disabled" 
                                wire:target="file_excel, import"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                            
                            <!-- Teks Normal (Hanya muncul jika tidak ada loading) -->
                            <span wire:loading.remove wire:target="file_excel, import">Mulai Import</span>
                            
                            <!-- Teks Loading saat Upload File -->
                            <span wire:loading wire:target="file_excel" class="flex items-center gap-2">
                                @include('partials.spinner') Mengunggah File...
                            </span>

                            <!-- Teks Loading saat Proses Import -->
                            <span wire:loading wire:target="import" class="flex items-center gap-2">
                                @include('partials.spinner') Memproses Data...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>