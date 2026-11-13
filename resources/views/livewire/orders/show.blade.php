<div>
    <!-- HEADER DETAIL PESANAN -->
    <a href="{{ route('orders.my') }}" class="text-indigo-600 hover:underline text-sm font-medium mb-2 inline-block">&larr; Kembali ke Pesanan Saya</a>
        <!-- HEADER RINGKASAN PESANAN -->
    <div class="mb-6 bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            
            <!-- KIRI: Info Pesanan -->
            <div>
                <!-- Label & Nomor Pesanan -->
                <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Nomor Pesanan</div>
                <h1 class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">
                    {{ $order->kode_order }}
                </h1>
                
                <!-- Waktu Pembuatan -->
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-zinc-700 dark:text-zinc-300">
                    <span>Dibuat: <strong class="font-bold">{{ $order->created_at->translatedFormat('d F Y') }}</strong> pukul <strong class="font-bold">{{ $order->created_at->format('H:i') }}</strong></span>
                    
                    <!-- Sembunyikan Sales PIC jika tidak ada / bernilai 'Sistem' -->
                    @if($order->sales && $order->sales->name !== 'Sistem')
                        <span class="hidden md:inline text-zinc-300">|</span>
                        <span>Sales PIC: <strong class="font-bold">{{ $order->sales->name }}</strong></span>
                    @endif
                </div>
            </div>

            <!-- KANAN: Ringkasan Status Cerdas -->
            <div class="flex flex-wrap gap-4">
                
                @php
                    $totalPesan = $order->items->sum('qty');
                    $totalKirim = $order->items->sum('qty_terkirim');
                    $isLunas = $order->payment_status->value === 'paid';
                    $isTerkirimSemua = ($totalKirim > 0 && $totalKirim >= $totalPesan);
                @endphp

                <!-- LOGIKA 1: JIKA SELESAI (Lunas + Terkirim Semua) -->
                @if($isLunas && $isTerkirimSemua && in_array($order->status->value, ['diproses', 'dikirim', 'selesai']))
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-zinc-400 mb-1 tracking-wider">Status Akhir</span>
                        <span class="px-4 py-2 bg-green-100 text-green-800 rounded-lg text-sm font-bold shadow-sm border border-green-200">
                            ✔ Pesanan Selesai
                        </span>
                    </div>

                <!-- LOGIKA 2: JIKA MENUNGGU / BATAL (Hanya Tampil Status Pesanan) -->
                @elseif(in_array($order->status->value, ['menunggu_approval', 'batal']))
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-zinc-400 mb-1 tracking-wider">Status Pesanan</span>
                        @if($order->status->value === 'menunggu_approval')
                            <span class="px-4 py-2 bg-amber-100 text-amber-800 rounded-lg text-sm font-bold shadow-sm border border-amber-200">Menunggu Persetujuan</span>
                        @else
                            <span class="px-4 py-2 bg-red-100 text-red-800 rounded-lg text-sm font-bold shadow-sm border border-red-200">Dibatalkan</span>
                        @endif
                    </div>

                <!-- LOGIKA 3: SEDANG BERJALAN (Tampilkan 3 Status Lengkap) -->
                @else
                    <!-- Status Pesanan -->
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-zinc-400 mb-1 tracking-wider">Pesanan</span>
                        <span class="px-3 py-1.5 bg-blue-100 text-blue-800 rounded-md text-xs font-bold border border-blue-200 text-center">{{ $order->status->label() }}</span>
                    </div>

                    <!-- Status Pembayaran -->
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-zinc-400 mb-1 tracking-wider">Pembayaran</span>
                        @if($order->payment_status->value === 'paid')
                            <span class="px-3 py-1.5 bg-green-100 text-green-800 rounded-md text-xs font-bold border border-green-200 text-center">Lunas</span>
                        @elseif($order->payment_status->value === 'partial')
                            <span class="px-3 py-1.5 bg-amber-100 text-amber-800 rounded-md text-xs font-bold border border-amber-200 text-center">DP / Sebagian</span>
                        @else
                            <span class="px-3 py-1.5 bg-red-100 text-red-800 rounded-md text-xs font-bold border border-red-200 text-center">Belum Bayar</span>
                        @endif
                    </div>

                    <!-- Status Pengiriman -->
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-zinc-400 mb-1 tracking-wider">Pengiriman</span>
                        @if($totalKirim == 0)
                            <span class="px-3 py-1.5 bg-zinc-100 text-zinc-800 rounded-md text-xs font-bold border border-zinc-200 text-center">Belum Dikirim</span>
                        @elseif($totalKirim < $totalPesan)
                            <span class="px-3 py-1.5 bg-indigo-100 text-indigo-800 rounded-md text-xs font-bold border border-indigo-200 text-center">Dikirim Sebagian</span>
                        @else
                            <span class="px-3 py-1.5 bg-green-100 text-green-800 rounded-md text-xs font-bold border border-green-200 text-center">Terkirim Semua</span>
                        @endif
                    </div>
                @endif
                
            </div>
            
        </div>
    </div>

    <!-- NAVIGASI TAB (Alpine.js) -->
    <div x-data="{ tab: 'rincian' }" class="mb-6">
        <div class="flex border-b border-zinc-200 dark:border-zinc-700">
            <button @click="tab = 'rincian'" :class="{ 'border-indigo-600 text-indigo-600': tab === 'rincian', 'border-transparent text-zinc-500': tab !== 'rincian' }" class="px-6 py-3 text-sm font-medium border-b-2 transition-colors">
                Rincian Pesanan
            </button>
            <!-- Tab Pembayaran: Muncul jika sudah dikonfirmasi (atau dibatalkan agar bisa lihat riwayat) -->
            @if(in_array($order->status->value, ['dikonfirmasi', 'diproses', 'dikirim', 'selesai', 'batal']))
                <button @click="tab = 'pembayaran'" :class="{ 'border-indigo-600 text-indigo-600': tab === 'pembayaran', 'border-transparent text-zinc-500': tab !== 'pembayaran' }" class="px-6 py-3 text-sm font-medium border-b-2 transition-colors">
                    Pembayaran
                </button>
            <!-- Tab Pengiriman: Muncul HANYA jika barang sudah mulai dikirim atau selesai -->
                <button @click="tab = 'pengiriman'" :class="{ 'border-indigo-600 text-indigo-600': tab === 'pengiriman', 'border-transparent text-zinc-500': tab !== 'pengiriman' }" class="px-6 py-3 text-sm font-medium border-b-2 transition-colors">
                    Pengiriman
                </button>
            @endif
        </div>

        <!-- KONTEN TAB: RINCIAN -->
        <div x-show="tab === 'rincian'" class="mt-6 bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Rincian Barang</h3>
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700 text-sm text-zinc-500">
                        <th class="py-2">Produk</th>
                        <th class="py-2">Harga</th>
                        <th class="py-2">Qty</th>
                        <th class="py-2">Satuan</th>
                        <th class="py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr class="border-b border-zinc-100 dark:border-zinc-700/50">
                            <td class="py-3 text-zinc-900 dark:text-white">{{ $item->nama_produk }}</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $item->qty }}</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $item->product->satuan }}</td>
                            <td class="py-3 text-right font-medium text-zinc-900 dark:text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="mt-6 border-t border-zinc-200 dark:border-zinc-700 pt-4 space-y-2 w-1/2 ml-auto">
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500">Subtotal</span>
                    <span class="text-zinc-900 dark:text-white">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500">Diskon</span>
                    <span class="text-zinc-900 dark:text-white">{{ number_format($order->diskon, 0, ',', '.') }}%</span>
                    <span class="text-zinc-900 dark:text-white">Rp {{ number_format($order->diskon * $order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500">PPN</span>
                    <span class="text-zinc-900 dark:text-white">Rp {{ number_format($order->ppn, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-lg pt-2 border-t border-zinc-100 dark:border-zinc-700">
                    <span class="text-zinc-900 dark:text-white">Grand Total</span>
                    <span class="text-indigo-600 dark:text-indigo-400">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($order->invoice)
                <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-200 dark:border-blue-800 flex justify-between items-center">
                    <p class="text-blue-800 dark:text-blue-300 font-medium">Invoice: <span class="font-bold font-mono">{{ $order->invoice->nomor_invoice }}</span></p>
                    <button type="button" 
                            onclick="document.getElementById('invoice-modal').classList.remove('hidden');"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm">
                        Lihat Invoice
                    </button>
                </div>
            @endif
        </div>

        <!-- KONTEN TAB: PEMBAYARAN -->
        <div x-show="tab === 'pembayaran'" class="mt-6 space-y-6" style="display: none;">
            
            <!-- Ringkasan Pembayaran -->
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <h3 class="font-bold text-lg text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2">Ringkasan Pembayaran</h3>
                
                @php
                    $sudahDibayar = $order->payments->where('status', 'verified')->sum('jumlah');
                @endphp

                <!-- KOTAK RINGKASAN TAGIHAN -->
                <div class="space-y-3 mb-6 bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-lg border border-zinc-100 dark:border-zinc-700">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-zinc-500 dark:text-zinc-400">Subtotal</span>
                        <span class="font-bold text-zinc-900 dark:text-white">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($order->diskon > 0)
                    <div class="flex justify-between items-center text-sm text-green-600">
                        <span>Diskon</span>
                        <span class="font-bold">- Rp {{ number_format($order->diskon_tipe === 'persen' ? ($order->subtotal * $order->diskon / 100) : $order->diskon, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    <div class="flex justify-between items-center text-sm">
                        <span class="text-zinc-500 dark:text-zinc-400">PPN</span>
                        <span class="font-bold text-zinc-900 dark:text-white">Rp {{ number_format($order->ppn, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center pt-3 border-t border-zinc-200 dark:border-zinc-700">
                        <span class="font-bold text-zinc-700 dark:text-zinc-300">Sisa Tagihan</span>
                        <span class="font-bold text-xl text-red-600">Rp {{ number_format($order->sisaTagihan(), 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($order->status->value === 'menunggu_approval')
                    <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm text-amber-800">Menunggu persetujuan admin sebelum pembayaran dapat dilakukan.</p>
                    </div>
                @elseif($order->status->value === 'batal')
                    <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm text-red-800">Pesanan ini telah dibatalkan.</p>
                    </div>
                @else
                    <!-- Form Upload -->
                    @if($order->sisaTagihan() > 0 && $order->payments->where('status', 'pending')->count() == 0)
                        <div class="mt-2">
                            <!-- INSTRUKSI TRANSFER (MULTI-REKENING) -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg mb-4 border border-blue-200 dark:border-blue-800">
                                <p class="text-sm text-blue-800 dark:text-blue-300 font-medium mb-3">Silakan transfer ke salah satu rekening berikut:</p>
                                
                                @php
                                    $activeBanks = \App\Models\Setting::get('bank_accounts', []);
                                    $activeBanks = collect($activeBanks)->where('is_active', true);
                                @endphp

                                <div class="space-y-3">
                                    @foreach($activeBanks as $bank)
                                        <div class="flex justify-between items-center bg-white dark:bg-zinc-800 p-3 rounded-md border border-blue-100 dark:border-zinc-700">
                                            <div>
                                                <div class="font-bold text-zinc-900 dark:text-white">{{ $bank['bank_name'] }} - {{ $bank['account_number'] }}</div>
                                                <div class="text-xs text-zinc-500">a/n {{ $bank['holder'] }}</div>
                                            </div>
                                            <button type="button" onclick="navigator.clipboard.writeText('{{ $bank['account_number'] }}'); alert('Nomor rekening disalin!')" class="p-2 text-zinc-400 hover:text-indigo-600 transition" title="Salin Rekening">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>                            

                            <form wire:submit="uploadBukti" class="space-y-5">
                                <!-- INPUT NOMINAL -->
                                <div>
                                    <div class="flex justify-between items-end mb-2">
                                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300">Nominal Transfer (Rp)</label>
                                        <!-- TOMBOL PINTASAN DI KANAN ATAS -->
                                        <div class="flex gap-1">
                                            <button type="button" wire:click="$set('nominal_bayar', {{ $order->sisaTagihan() }})" class="px-2 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-bold rounded border border-indigo-200 hover:bg-indigo-100 transition">Lunas</button>
                                            <button type="button" wire:click="$set('nominal_bayar', {{ $order->sisaTagihan() / 2 }})" class="px-2 py-1 bg-zinc-100 text-zinc-700 text-[10px] font-bold rounded border border-zinc-300 hover:bg-zinc-200 transition">50%</button>
                                        </div>
                                    </div>
                                    
                                    <input type="number" wire:model="nominal_bayar" class="w-full px-4 py-2.5 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                    @error('nominal_bayar') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- KUSTOMISASI TOMBOL UPLOAD -->
                                <div>
                                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Bukti Transfer</label>
                                    
                                    <div class="relative">
                                        <input type="file" wire:model="bukti_transfer" id="bukti_transfer" class="hidden" accept="image/*,.pdf">
                                        
                                        <label for="bukti_transfer" class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-xl cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition">
                                            <svg class="w-6 h-6 text-zinc-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                            <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Pilih File Bukti Transfer</span>
                                        </label>
                                    </div>

                                    @error('bukti_transfer') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                                    
                                    <!-- LOADING STATE & PREVIEW -->
                                    <div wire:loading wire:target="bukti_transfer" class="mt-3 text-sm text-indigo-600 flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Mengunggah...
                                    </div>
                                    
                                    @if ($bukti_transfer)
                                        <div class="mt-3 p-2 border border-zinc-200 dark:border-zinc-700 rounded-lg inline-block bg-zinc-50 dark:bg-zinc-900">
                                            <p class="text-xs text-green-600 font-bold mb-1">File Terpilih:</p>
                                            <img src="{{ $bukti_transfer->temporaryUrl() }}" class="h-16 object-contain rounded">
                                        </div>
                                    @endif
                                </div>
                                
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-lg font-bold transition shadow-sm">
                                    Kirim Bukti Pembayaran
                                </button>
                            </form>
                        </div>
                    @elseif($order->payments->where('status', 'pending')->count() > 0)
                        <div class="flex items-start gap-3 p-4 bg-blue-50 border border-blue-200 rounded-lg mt-4">
                            <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-sm text-blue-800">Bukti transfer Anda sedang dalam proses verifikasi oleh tim Finance. Mohon tunggu.</p>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Riwayat Pembayaran -->
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Riwayat Pembayaran</h3>
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-zinc-200 text-sm text-zinc-500">
                            <th class="py-2">Tanggal</th>
                            <th class="py-2">Nominal</th>
                            <th class="py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->payments as $payment)
                            <tr class="border-b border-zinc-100 text-sm">
                                <td class="py-3">{{ $payment->created_at->format('d M Y') }}</td>
                                <td class="py-3 font-medium">Rp {{ number_format($payment->jumlah, 0, ',', '.') }}</td>
                                <td class="py-3">
                                    <span class="{{ $payment->status->value === 'verified' ? 'text-green-600' : ($payment->status->value === 'rejected' ? 'text-red-600' : 'text-amber-600') }}">
                                        {{ $payment->status->label() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-zinc-500">Belum ada riwayat pembayaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- KONTEN TAB: PENGIRIMAN -->
        <div x-show="tab === 'pengiriman'" class="mt-6 space-y-6" style="display: none;">
            
                        <!-- 1. TABEL STATUS BARANG -->
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Status Pengiriman Barang</h3>
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700 text-sm text-zinc-500">
                            <th class="py-2">Nama Barang</th>
                            <th class="py-2 text-center">Qty</th>
                            <th class="py-2 text-center text-red-600">Belum Dikirim</th>
                            <th class="py-2 text-center text-blue-600">Dikirim</th>
                            <th class="py-2 text-center text-green-600">Terkirim</th>
                            <th class="pb-2 text-center">Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            @php 
                                // Hitung qty yang sedang dikirim (belum diterima)
                                $qtyDikirim = $order->shipments
                                    ->filter(fn($s) => $s->status->value === 'dikirim')
                                    ->flatMap->items
                                    ->where('order_item_id', $item->id)
                                    ->sum('qty_dikirim');

                                // Hitung qty yang sudah diterima
                                $qtyTerkirim = $order->shipments
                                    ->filter(fn($s) => $s->status->value === 'diterima')
                                    ->flatMap->items
                                    ->where('order_item_id', $item->id)
                                    ->sum('qty_dikirim');

                                // Sisa belum dikirim (Total - Dikirim - Terkirim)
                                $sisa = $item->qty - $qtyDikirim - $qtyTerkirim;
                            @endphp
                            <tr class="border-b border-zinc-100 dark:border-zinc-700/50">
                                <td class="py-3 text-sm text-zinc-900 dark:text-white">{{ $item->nama_produk }}</td>
                                <td class="py-3 text-sm text-center font-medium">{{ $item->qty }}</td>
                                <td class="py-3 text-sm text-center font-bold text-red-600">
                                    {{ $sisa > 0 ? $sisa : '-' }}
                                </td>
                                <td class="py-3 text-sm text-center font-bold text-blue-600">
                                    {{ $qtyDikirim > 0 ? $qtyDikirim : '-' }}
                                </td>
                                <td class="py-3 text-sm text-center font-bold text-green-600">
                                    {{ $qtyTerkirim > 0 ? $qtyTerkirim : '-' }}
                                </td>
                                <td class="py-2 text-sm text-center text-zinc-500">{{ $item->orderItem->product->satuan ?? 'pcs' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- 2. RIWAYAT PENGIRIMAN (ACCORDION) -->
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Riwayat Pengiriman</h3>
                
                @if($order->shipments->count() > 0)
                    <div class="space-y-3">
                        @foreach($order->shipments as $index => $shipment)
                            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden" x-data="{ open: false }">
                                
                                <!-- Header Card -->
                                <div @click="open = !open" class="p-3 flex justify-between items-center cursor-pointer bg-zinc-50 dark:bg-zinc-900/50 hover:bg-zinc-100 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-indigo-100 text-indigo-700 font-bold w-8 h-8 flex items-center justify-center rounded-full text-sm">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-sm text-zinc-900 dark:text-white">{{ $shipment->ekspedisi }} - {{ $shipment->no_resi ?? 'Tanpa Resi' }}</div>
                                            <div class="text-xs text-zinc-500">
                                                Dikirim: {{ $shipment->shipped_at ? $shipment->shipped_at->format('d M Y') : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('surat-jalan.download', $shipment->id) }}" target="_blank" @click.stop class="text-xs font-medium text-indigo-600 hover:underline">Lihat Surat Jalan</a>
                                        <svg :class="{'rotate-180': open}" class="w-4 h-4 text-zinc-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Isi Detail Resi -->
                                <div x-show="open" x-collapse style="display: none;" class="border-t border-zinc-200 dark:border-zinc-700 p-4 bg-zinc-50/50 dark:bg-zinc-900/20">
                                    
                                    <table class="w-full text-left border-collapse mb-4">
                                        <!-- Tambahkan Header Tabel -->
                                        <thead>
                                            <tr class="border-b border-zinc-200 dark:border-zinc-700 text-xs text-zinc-500 uppercase">
                                                <th class="pb-2">Nama Barang</th>
                                                <th class="pb-2 text-center">Qty</th>
                                                <th class="pb-2 text-center">Satuan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                                            @foreach($shipment->items as $item)
                                                <tr class="hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                                                    <td class="py-2 text-sm text-zinc-900 dark:text-white">{{ $item->orderItem->nama_produk }}</td>
                                                    <td class="py-2 text-sm text-center font-bold text-indigo-600">{{ $item->qty_dikirim }}</td>
                                                    <td class="py-2 text-sm text-center text-zinc-500">{{ $item->orderItem->product->satuan ?? 'pcs' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <!-- STATUS & TOMBOL KONFIRMASI -->
                                    <div class="pt-3 border-t border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                                        @if($shipment->status->value === 'diterima')
                                            <span class="text-sm font-bold text-green-600">
                                                ✔ Diterima pada: {{ $shipment->received_at ? $shipment->received_at->format('d M Y') : '-' }}
                                            </span>
                                        @else
                                            <span class="text-sm font-bold text-amber-600">🚚 Sedang di perjalanan</span>
                                            <button wire:click="konfirmasiDiterima({{ $shipment->id }})" wire:confirm="Konfirmasi bahwa barang sudah Anda terima?" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-xs font-bold transition">
                                                Konfirmasi Barang Telah Diterima
                                            </button>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-zinc-500">Belum ada riwayat pengiriman.</p>
                @endif
            </div>
        </div>
    </div>

    @if($order->status->value === 'menunggu_approval')
        <div class="mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700 text-right">
            <button wire:click="batalkanPesanan" wire:confirm="Yakin ingin membatalkan pesanan ini?" class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-bold transition">
                Batalkan Pesanan
            </button>
        </div>
    @endif

    <!-- MODAL INVOICE CUSTOMER -->
    <div id="invoice-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-2xl w-full max-w-4xl relative my-8">
            
            <!-- Tombol Close Modal -->
            <button onclick="document.getElementById('invoice-modal').classList.add('hidden');" class="absolute top-4 right-4 text-zinc-500 hover:text-red-500 bg-zinc-100 hover:bg-red-50 p-2 rounded-full transition z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <!-- ISI INVOICE (Hanya memanggil isi kontennya) -->
            <div class="p-8 md:p-12 text-zinc-900 dark:text-zinc-200">
                @include('pdf.partials.invoice-content', ['order' => $order])
            </div>
            
        </div>
    </div>
</div>