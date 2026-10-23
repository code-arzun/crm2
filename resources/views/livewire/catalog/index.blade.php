<div>
    <!-- Header Halaman -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Katalog Produk</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Pilih produk atau jasa yang Anda butuhkan.</p>
        </div>
        
        <div class="w-full md:w-1/3">
            <input 
                wire:model.live.debounce.300ms="search"
                type="text" 
                placeholder="Cari nama atau kode produk..." 
                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            >
        </div>
    </div>

    <!-- Grid Produk -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($products as $product)
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden flex flex-col hover:shadow-md transition">
                
                <!-- Tempat Gambar (Placeholder) -->
                <div class="h-40 bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center">
                    <span class="text-zinc-400 text-sm">Gambar Belum Tersedia</span>
                </div>

                <div class="p-4 flex-1 flex flex-col">
                    <div class="text-xs text-zinc-500 mb-1">{{ $product->kode }}</div>
                    <h3 class="font-bold text-zinc-900 dark:text-white leading-tight mb-2 line-clamp-2">{{ $product->nama }}</h3>
                    
                    <div class="text-lg font-bold text-indigo-600 dark:text-indigo-400 mt-auto">
                        Rp {{ number_format($product->harga_default, 0, ',', '.') }}
                    </div>
                    
                    {{-- <div class="text-xs mt-1 mb-4 
                        @if($product->tipe->value === 'jasa') text-purple-600 @elseif($product->stok > 0) text-green-600 @else text-red-600 @endif">
                        @if($product->tipe->value === 'jasa')
                            Jasa / Layanan
                        @else
                            Stok: {{ $product->stok }} {{ $product->satuan }}
                        @endif
                    </div> --}}

                    <!-- Tombol Keranjang -->
                    <div class="mt-auto">
                        <button wire:click="addToCart({{ $product->id }})" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                            + Keranjang
                        </button>
                        
                        <!-- Teks info jumlah di keranjang -->
                        @if(isset($this->cart[$product->id]))
                            <div class="text-center text-xs text-indigo-600 mt-2 font-medium">
                                {{ $this->cart[$product->id]['qty'] }} di keranjang
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-10 text-center text-zinc-500">
                Produk tidak ditemukan.
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $products->links() }}
    </div>
</div>