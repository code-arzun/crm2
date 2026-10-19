<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Buat Pesanan Baru</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Pilih produk yang ingin Anda beli.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- KIRI: Pilih Produk & Keranjang -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Form Pilih Produk -->
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Pilih Produk</h3>
                
                <div class="flex gap-4 items-end">
                    <div class="flex-1">
                        <select wire:model="selectedProductId" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">
                                    {{ $p->kode }} - {{ $p->nama }} (Rp {{ number_format($p->harga_default, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('selectedProductId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="w-24">
                        <input wire:model="qty" type="number" min="1" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white" placeholder="Qty">
                        @error('qty') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <button wire:click="addToCart" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition h-10">
                        Tambah
                    </button>
                </div>
            </div>

            <!-- Tabel Keranjang -->
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Keranjang Belanja</h3>
                
                <div class="overflow-x-auto">
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
                                    <td class="py-3 text-center text-sm">{{ $item['qty'] }}</td>
                                    <td class="py-3 text-right font-medium">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                    <td class="py-3 text-right">
                                        <button wire:click="removeFromCart({{ $item['id'] }})" class="text-red-500 hover:text-red-700 text-sm">Batal</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-zinc-500">Belum ada produk di keranjang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @error('cart') <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span> @enderror
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

                <button wire:click="submitOrder" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-lg font-bold transition shadow-sm">
                    Buat Pesanan
                </button>
            </div>
        </div>

    </div>
</div>