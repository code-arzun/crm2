<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Checkout Pesanan</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Periksa kembali pesanan Anda sebelum melakukan checkout.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- KIRI: Keranjang Belanja -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-800 p-4 md:p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Keranjang Belanja</h3>
                
                <!-- ========================================== -->
                <!-- TAMPILAN DESKTOP (TABEL) -->
                <!-- ========================================== -->
                <div class="hidden md:block overflow-x-auto">
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
                                        <input type="number" min="1" value="{{ $item['qty'] }}" wire:change="updateQty({{ $item['id'] }}, $event.target.value)" class="w-16 px-2 py-1 text-center border border-zinc-300 dark:border-zinc-600 rounded-md bg-white dark:bg-zinc-900 text-sm">
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
                </div>

                <!-- ========================================== -->
                <!-- TAMPILAN MOBILE (CARD) -->
                <!-- ========================================== -->
                <div class="md:hidden space-y-3">
                    @forelse($cart as $item)
                        <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-3 bg-zinc-50 dark:bg-zinc-900/50">
                            <div class="flex justify-between items-start mb-2">
                                <div class="font-medium text-zinc-900 dark:text-white text-sm">{{ $item['nama'] }}</div>
                                <button wire:click="removeFromCart({{ $item['id'] }})" class="text-red-500 text-xs font-bold ml-2">Hapus</button>
                            </div>
                            <div class="flex justify-between items-center mt-3">
                                <div class="text-sm font-bold text-indigo-600">Rp {{ number_format($item['harga'], 0, ',', '.') }}</div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-zinc-500">Qty:</span>
                                    <input type="number" min="1" value="{{ $item['qty'] }}" wire:change="updateQty({{ $item['id'] }}, $event.target.value)" class="w-16 px-2 py-1 text-center border border-zinc-300 dark:border-zinc-600 rounded-md bg-white dark:bg-zinc-900 text-sm">
                                </div>
                            </div>
                            <div class="flex justify-between items-center mt-2 pt-2 border-t border-zinc-200 dark:border-zinc-700/50">
                                <span class="text-xs text-zinc-500">Subtotal:</span>
                                <span class="text-sm font-bold text-zinc-900 dark:text-white">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-zinc-500 text-sm">
                            Keranjang Anda masih kosong. <br>
                            <a href="{{ route('catalog.index') }}" class="text-indigo-600 hover:underline mt-2 inline-block">Kembali ke Katalog</a>
                        </div>
                    @endforelse
                </div>

                @error('cart') <span class="text-red-500 text-sm mt-4 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- KANAN: Ringkasan & Checkout -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-zinc-800 p-4 md:p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm lg:sticky lg:top-6">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Ringkasan Pesanan</h3>
                
                <div class="space-y-3 border-b border-zinc-200 dark:border-zinc-700 pb-4 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">Subtotal</span>
                        <span class="font-medium text-zinc-900 dark:text-white">Rp {{ number_format($this->getTotalProperty(), 0, ',', '.') }}</span>
                    </div>

                    <!-- TUKAR POIN -->
                    @if($totalPoinCustomer > 0)
                        <div class="bg-amber-50 dark:bg-amber-900/20 p-3 rounded-lg border border-amber-200 dark:border-amber-800">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-bold text-amber-800 dark:text-amber-300">Poin Anda: {{ $totalPoinCustomer }}</span>
                            </div>
                            <div class="flex gap-2 items-center">
                                <input type="number" wire:model.live="poin_ditukar" min="0" max="{{ $totalPoinCustomer }}" class="w-20 px-2 py-1 text-sm border rounded border-amber-300 dark:bg-zinc-900">
                                <span class="text-xs text-amber-700">poin ditukar</span>
                            </div>
                            <p class="text-[10px] text-amber-600 mt-1">*1 Poin = Rp 100</p>
                        </div>
                    @endif
                    
                    <!-- Logika PPN -->
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">PPN</span>
                        <span class="font-medium text-zinc-900 dark:text-white">
                            @php $ppn = \App\Models\Setting::get('ppn_enabled', false) ? $this->getTotalProperty() * (\App\Models\Setting::get('ppn_persen', 11) / 100) : 0; @endphp
                            Rp {{ number_format($ppn, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-4">
                    <span class="font-bold text-zinc-900 dark:text-white">Grand Total</span>
                    <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                        Rp {{ number_format($this->getTotalProperty() + $ppn, 0, ',', '.') }}
                    </span>
                </div>

                <!-- METODE PENGIRIMAN -->
                <div class="mb-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Metode Pengiriman</label>
                    <select wire:model.live="metode_pengiriman" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="ekspedisi">Ekspedisi / Kurir</option>
                        <option value="ambil_sendiri">Ambil Sendiri di Gudang</option>
                    </select>
                    @if($metode_pengiriman === 'ekspedisi')
                        <p class="text-xs text-zinc-500 mt-1">*Biaya ongkir akan diinformasikan oleh Admin.</p>
                    @else
                        <p class="text-xs text-green-600 font-bold mt-1">*Gratis biaya pengiriman.</p>
                    @endif
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Catatan (Opsional)</label>
                    <textarea wire:model="catatan" rows="2" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white text-sm" placeholder="Contoh: Tolong dikirim cepat"></textarea>
                </div>

                <button wire:click="konfirmasiPesanan" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-lg font-bold transition shadow-sm {{ empty($cart) ? 'opacity-50 cursor-not-allowed' : '' }}" @if(empty($cart)) disabled @endif>
                    Buat Pesanan
                </button>
            </div>
        </div>

    </div>

    <!-- MODAL KONFIRMASI PESANAN -->
    @if($isConfirmModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-2xl w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col">
                
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center bg-zinc-50 dark:bg-zinc-900/50">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Konfirmasi Pesanan</h3>
                    <button wire:click="$set('isConfirmModalOpen', false)" class="text-zinc-400 hover:text-zinc-600 text-2xl leading-none">&times;</button>
                </div>
                
                <div class="p-6 overflow-y-auto">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4">Apakah Anda yakin data pesanan berikut sudah benar?</p>
                    
                    <!-- Rincian Ringkas -->
                    <div class="bg-zinc-50 dark:bg-zinc-900/50 rounded-lg p-4 mb-4 border border-zinc-200 dark:border-zinc-700 max-h-48 overflow-y-auto">
                        <table class="w-full text-left">
                            <tbody>
                                @foreach($cart as $item)
                                    <tr class="border-b border-zinc-100 dark:border-zinc-700/50 last:border-0">
                                        <td class="py-2 text-sm text-zinc-900 dark:text-white">{{ $item['nama'] }}</td>
                                        <td class="py-2 text-sm text-center text-zinc-500">{{ $item['qty'] }}x</td>
                                        <td class="py-2 text-sm text-right font-medium">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Total -->
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-zinc-500">Subtotal</span>
                        <span class="text-sm font-medium">Rp {{ number_format($this->getTotalProperty(), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-4 pb-4 border-b border-zinc-200 dark:border-zinc-700">
                        <span class="text-sm text-zinc-500">PPN</span>
                        <span class="text-sm font-medium">
                            @php $ppn = \App\Models\Setting::get('ppn_enabled', false) ? $this->getTotalProperty() * (\App\Models\Setting::get('ppn_persen', 11) / 100) : 0; @endphp
                            Rp {{ number_format($ppn, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center mb-6">
                        <span class="font-bold text-zinc-900 dark:text-white">Grand Total</span>
                        <span class="text-xl font-bold text-indigo-600">
                            Rp {{ number_format($this->getTotalProperty() + $ppn, 0, ',', '.') }}
                        </span>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex gap-3">
                        <button wire:click="$set('isConfirmModalOpen', false)" class="flex-1 px-4 py-2.5 text-sm font-medium text-zinc-700 bg-zinc-100 hover:bg-zinc-200 rounded-lg transition">
                            Cek Kembali
                        </button>
                        <button wire:click="prosesPesanan" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg text-sm font-bold transition shadow-sm flex justify-center items-center">
                            <span wire:loading.remove wire:target="prosesPesanan">Ya, Buat Pesanan</span>
                            <span wire:loading wire:target="prosesPesanan" class="flex items-center gap-2">
                                @include('partials.spinner') Memproses...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>