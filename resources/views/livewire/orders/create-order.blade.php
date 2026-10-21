<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Checkout Pesanan</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Periksa kembali pesanan Anda sebelum melakukan checkout.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- Banner Peringatan Profil -->
      @if(auth()->user()->customer && (!auth()->user()->customer->alamat || !auth()->user()->customer->nama_perusahaan))
          <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-lg flex items-center justify-between">
              <div>
                  <strong>Profil Anda belum lengkap!</strong> Anda harus melengkapi Alamat dan Nama Perusahaan sebelum bisa melakukan checkout.
              </div>
              <a href="{{ route('customer.lengkapi') }}" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-md text-sm font-bold transition">
                  Lengkapi Sekarang
              </a>
          </div>
      @endif
        
        <!-- KIRI: Keranjang Belanja -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Keranjang Belanja</h3>
                
                <!-- TABEL PRODUK -->
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700 text-sm text-zinc-500">
                            <th class="py-2">Produk</th>
                            <th class="py-2">Harga</th>
                            <th class="py-2 text-center">Qty</th>
                            <th class="py-2 text-right">Subtotal</th>
                            <th class="py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cart as $item)
                            <tr class="border-b border-zinc-100 dark:border-zinc-700/50">
                                <td class="py-3 font-medium text-zinc-900 dark:text-white">{{ $item['nama'] }}</td>
                                <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                                
                                <td class="py-3 text-center">
                                    <input 
                                        type="number" 
                                        min="1" 
                                        value="{{ $item['qty'] }}"
                                        wire:change="updateQty({{ $item['id'] }}, $event.target.value)"
                                        class="w-16 px-2 py-1 text-center border border-zinc-300 dark:border-zinc-600 rounded-md bg-white dark:bg-zinc-900 text-sm"
                                    >
                                </td>

                                <td class="py-3 text-right font-medium text-zinc-900 dark:text-white">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                <td class="py-3 text-right">
                                    <button wire:click="removeFromCart({{ $item['id'] }})" class="text-red-500 hover:text-red-700 text-sm font-medium">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-zinc-500">
                                    Keranjang Anda masih kosong. <br>
                                    <a href="{{ route('catalog.index') }}" class="text-indigo-600 hover:underline mt-2 inline-block">Kembali ke Katalog</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- KOTAK PENCARIAN (Dipisah dari tabel, diberi jarak mt-8) -->
                <div class="mt-8">
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Tambah Produk Lain:</label>
                    
                    <div class="relative">
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="searchProduct"
                            placeholder="Ketik nama atau kode produk untuk mencari..." 
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        >
    
                        <!-- DROPDOWN HASIL PENCARIAN -->
                        @if(count($this->searchResults) > 0)
                            <div class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-xl overflow-hidden">
                                @foreach($this->searchResults as $result)
                                    <button 
                                        wire:click="addProductFromSearch({{ $result->id }})"
                                        class="w-full text-left px-4 py-3 hover:bg-indigo-50 dark:hover:bg-zinc-700 border-b border-zinc-100 dark:border-zinc-700/50 last:border-0 flex justify-between items-center transition"
                                    >
                                        <div>
                                            <div class="font-medium text-zinc-900 dark:text-white">{{ $result->nama }}</div>
                                            <div class="text-xs text-zinc-500">{{ $result->kode }}</div>
                                        </div>
                                        <div class="font-bold text-indigo-600">Rp {{ number_format($result->harga_default, 0, ',', '.') }}</div>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        
                        @if(strlen($searchProduct) >= 2 && count($this->searchResults) === 0)
                            <div class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-xl p-4 text-center text-sm text-zinc-500">
                                Produk tidak ditemukan.
                            </div>
                        @endif
                    </div>
                </div>

                @error('cart') <span class="text-red-500 text-sm mt-4 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- KANAN: Ringkasan & Checkout -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm sticky top-6">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Ringkasan Pesanan</h3>
                
                <div class="space-y-3 border-b border-zinc-200 dark:border-zinc-700 pb-4 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">Subtotal</span>
                        <span class="font-medium text-zinc-900 dark:text-white">Rp {{ number_format($this->getTotalProperty(), 0, ',', '.') }}</span>
                    </div>
                    <!-- Logika PPN -->
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">PPN</span>
                        <span class="font-medium text-zinc-900 dark:text-white">
                            @php $ppn = \App\Models\Setting::get('ppn_enabled', false) ? $this->getTotalProperty() * (\App\Models\Setting::get('ppn_persen', 11) / 100) : 0; @endphp
                            Rp {{ number_format($ppn, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-6">
                    <span class="font-bold text-zinc-900 dark:text-white">Total</span>
                    <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                        Rp {{ number_format($this->getTotalProperty() + $ppn, 0, ',', '.') }}
                    </span>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Catatan (Opsional)</label>
                    <textarea wire:model="catatan" rows="2" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white text-sm" placeholder="Contoh: Tolong dikirim cepat"></textarea>
                </div>

                <button wire:click="submitOrder" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-lg font-bold transition shadow-sm {{ empty($cart) ? 'opacity-50 cursor-not-allowed' : '' }}" @if(empty($cart)) disabled @endif>
                    Buat Pesanan
                </button>
            </div>
        </div>

    </div>
</div>