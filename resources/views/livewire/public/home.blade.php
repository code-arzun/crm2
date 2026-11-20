<div>
    <!-- HERO SECTION -->
    <section class="bg-indigo-900 text-white py-12 md:py-20 px-4 relative overflow-hidden">
        <div class="absolute inset-0 bg-indigo-800 opacity-50"></div>
        <div class="max-w-7xl mx-auto relative z-10 text-center">
            <h1 class="text-2xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6 leading-tight">Solusi Pengadaan Barang & Jasa Terpercaya</h1>
            <p class="text-sm md:text-lg text-indigo-200 mb-6 md:mb-8 max-w-2xl mx-auto">
                Kami menyediakan kebutuhan operasional bisnis Anda dengan kualitas terbaik dan harga bersaing.
            </p>
            <a href="{{ route('public.catalog') }}" class="inline-block bg-white text-indigo-900 font-bold px-6 py-2.5 md:px-8 md:py-3 rounded-full hover:bg-indigo-50 transition text-sm md:text-base">
                Lihat Katalog Produk
            </a>
        </div>
    </section>

    <!-- PRODUK TERLARIS -->
    <section class="py-10 md:py-16 px-4 max-w-7xl mx-auto">
        <div class="flex justify-between items-end mb-4 md:mb-8">
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-zinc-900 dark:text-white">Produk Terlaris</h2>
                <p class="text-xs md:text-sm text-zinc-500 mt-1">Paling banyak dipesan oleh pelanggan kami.</p>
            </div>
        </div>

        <div x-data="{ 
                scrollLeft() { $refs.carousel.scrollBy({ left: -250, behavior: 'smooth' }) },
                scrollRight() { $refs.carousel.scrollBy({ left: 250, behavior: 'smooth' }) }
             }" 
             class="relative">
            
            <button @click="scrollLeft" class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white dark:bg-zinc-800 shadow-lg rounded-full p-2 text-zinc-600 hover:text-indigo-600 hidden md:block -ml-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>

            <div x-ref="carousel" class="flex gap-3 md:gap-4 overflow-x-auto snap-x snap-mandatory pb-4" style="scrollbar-width: none; -ms-overflow-style: none;">
                @forelse($topProducts as $product)
                    <div class="min-w-[150px] md:min-w-[250px] snap-start bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden flex flex-col">
                        <div class="h-32 md:h-40 bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center">
                            <span class="text-zinc-400 text-xs md:text-sm">No Image</span>
                        </div>
                        <div class="p-3 md:p-4 flex-1 flex flex-col">
                            <h3 class="font-bold text-zinc-900 dark:text-white text-xs md:text-base line-clamp-2 h-8 md:h-auto">{{ $product->nama }}</h3>
                            <div class="text-indigo-600 font-bold mt-1 md:mt-2 text-sm md:text-base">Rp {{ number_format($product->harga_default, 0, ',', '.') }}</div>
                            <!-- Jika User Belum Login -->
                            @guest
                                <button onclick="window.dispatchEvent(new CustomEvent('open-guest-modal'))" class="mt-2 md:mt-3 w-full bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-center py-1.5 md:py-2 rounded-lg text-[10px] md:text-sm font-bold transition">Beli</button>
                            @endguest

                            <!-- Jika User Sudah Login -->
                            @auth
                                <a href="{{ route('catalog.index') }}" class="mt-2 md:mt-3 w-full bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-center py-1.5 md:py-2 rounded-lg text-[10px] md:text-sm font-bold transition">Beli</a>
                            @endauth
                        </div>
                    </div>
                @empty
                    <p class="text-zinc-500 text-sm">Belum ada produk.</p>
                @endforelse
            </div>

            <button @click="scrollRight" class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white dark:bg-zinc-800 shadow-lg rounded-full p-2 text-zinc-600 hover:text-indigo-600 hidden md:block -mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </section>

    <!-- PRODUK TERBARU -->
    <section class="py-10 md:py-16 px-4 max-w-7xl mx-auto bg-zinc-50 dark:bg-zinc-900/50 rounded-2xl">
        <div class="flex justify-between items-end mb-4 md:mb-8">
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-zinc-900 dark:text-white">Produk Terbaru</h2>
                <p class="text-xs md:text-sm text-zinc-500 mt-1">Katalog terbaru yang baru saja kami tambahkan.</p>
            </div>
        </div>

        <div x-data="{ 
                scrollLeft() { $refs.carousel2.scrollBy({ left: -250, behavior: 'smooth' }) },
                scrollRight() { $refs.carousel2.scrollBy({ left: 250, behavior: 'smooth' }) }
             }" 
             class="relative">
            
            <button @click="scrollLeft" class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white dark:bg-zinc-800 shadow-lg rounded-full p-2 text-zinc-600 hover:text-indigo-600 hidden md:block -ml-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>

            <div x-ref="carousel2" class="flex gap-3 md:gap-4 overflow-x-auto snap-x snap-mandatory pb-4" style="scrollbar-width: none; -ms-overflow-style: none;">
                @forelse($newProducts as $product)
                    <div class="min-w-[150px] md:min-w-[250px] snap-start bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden flex flex-col">
                        <div class="h-32 md:h-40 bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center">
                            <span class="text-zinc-400 text-xs md:text-sm">No Image</span>
                        </div>
                        <div class="p-3 md:p-4 flex-1 flex flex-col">
                            <h3 class="font-bold text-zinc-900 dark:text-white text-xs md:text-base line-clamp-2 h-8 md:h-auto">{{ $product->nama }}</h3>
                            <div class="text-indigo-600 font-bold mt-1 md:mt-2 text-sm md:text-base">Rp {{ number_format($product->harga_default, 0, ',', '.') }}</div>
                            <a href="{{ route('register') }}" class="mt-2 md:mt-3 w-full bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-center py-1.5 md:py-2 rounded-lg text-[10px] md:text-sm font-bold transition">Beli</a>
                        </div>
                    </div>
                @empty
                    <p class="text-zinc-500 text-sm">Belum ada produk.</p>
                @endforelse
            </div>

            <button @click="scrollRight" class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white dark:bg-zinc-800 shadow-lg rounded-full p-2 text-zinc-600 hover:text-indigo-600 hidden md:block -mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </section>

    <!-- TENTANG KAMI -->
    <section id="tentang" class="py-12 md:py-20 px-4 max-w-4xl mx-auto text-center">
        <h2 class="text-2xl md:text-3xl font-bold text-zinc-900 dark:text-white mb-3 md:mb-4">Tentang Kami</h2>
        <p class="text-sm md:text-lg text-zinc-600 dark:text-zinc-400">
            {{ $companyName }} adalah perusahaan yang bergerak di bidang penyediaan barang dan jasa untuk kebutuhan bisnis Anda. Kami berkomitmen memberikan kualitas terbaik dengan pelayanan yang profesional.
        </p>
        <p class="mt-3 md:mt-4 text-xs md:text-base text-zinc-500">{{ $companyAddress }}</p>
    </section>
</div>