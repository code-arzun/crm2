<div class="py-8 md:py-12 px-4 max-w-7xl mx-auto">
    <div class="text-center mb-8 md:mb-10">
        <h1 class="text-2xl md:text-3xl font-bold text-zinc-900 dark:text-white">Katalog Produk & Jasa</h1>
        <p class="text-sm md:text-base text-zinc-500 mt-2">Temukan kebutuhan bisnis Anda di sini.</p>
    </div>

    <!-- FILTER & SEARCH -->
    <div class="flex flex-col md:flex-row gap-3 md:gap-4 mb-6 md:mb-8">
        <div class="flex-1">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau kode produk..." class="w-full px-4 py-2.5 md:py-3 border border-zinc-300 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-sm md:text-base text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="md:w-64">
            <select wire:model.live="category_id" class="w-full px-4 py-2.5 md:py-3 border border-zinc-300 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800 text-sm md:text-base text-zinc-900 dark:text-white">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- GRID PRODUK -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-6">
        @forelse($products as $product)
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden flex flex-col hover:shadow-md transition">
                <div class="h-32 md:h-48 bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center">
                    <span class="text-zinc-400 text-xs md:text-sm">No Image</span>
                </div>
                <div class="p-3 md:p-4 flex-1 flex flex-col">
                    <div class="text-[10px] md:text-xs text-zinc-500 mb-1">{{ $product->kode }}</div>
                    <h3 class="font-bold text-zinc-900 dark:text-white text-xs md:text-base line-clamp-2 h-8 md:h-auto">{{ $product->nama }}</h3>
                    <div class="text-sm md:text-lg font-bold text-indigo-600 dark:text-indigo-400 mt-1 md:mt-2">
                        Rp {{ number_format($product->harga_default, 0, ',', '.') }}
                    </div>
                    
                    @guest
                        <button onclick="window.dispatchEvent(new CustomEvent('open-guest-modal'))" class="mt-3 md:mt-4 w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center py-1.5 md:py-2 rounded-lg text-[10px] md:text-sm font-bold transition">
                            Beli Sekarang
                        </button>
                    @endguest

                    @auth
                        <a href="{{ route('catalog.index') }}" class="mt-3 md:mt-4 w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center py-1.5 md:py-2 rounded-lg text-[10px] md:text-sm font-bold transition">
                            Beli Sekarang
                        </a>
                    @endauth
                </div>
            </div>
        @empty
            <div class="col-span-full py-10 text-center text-zinc-500 text-sm">Produk tidak ditemukan.</div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $products->links() }}
    </div>
</div>