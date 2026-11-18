<div>
    <!-- 1. HEADER RINGKASAN (Responsive) -->
    <div class="mb-6 bg-white dark:bg-zinc-800 p-4 md:p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            
            <div>
                <a href="{{ route('orders.my') }}" class="text-indigo-600 hover:underline text-sm font-medium mb-2 inline-block">&larr; Kembali</a>
                <div class="text-[10px] md:text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Nomor Pesanan</div>
                <h1 class="text-lg md:text-xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">
                    {{ $order->kode_order }}
                </h1>
                
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs md:text-sm text-zinc-700 dark:text-zinc-300">
                    <span>Dibuat: <strong class="font-bold">{{ $order->created_at->translatedFormat('d M Y') }}</strong> pukul <strong class="font-bold">{{ $order->created_at->format('H:i') }}</strong></span>
                    @if($order->sales && $order->sales->name !== 'Sistem')
                        <span class="hidden md:inline text-zinc-300">|</span>
                        <span>Sales PIC: <strong class="font-bold">{{ $order->sales->name }}</strong></span>
                    @endif
                </div>
            </div>

            <!-- Ringkasan Status (Tumpuk di HP, Sejajar di Desktop) -->
            <div class="flex flex-wrap gap-2 md:gap-4 w-full md:w-auto">
                
                @php
                    $totalPesan = $order->items->sum('qty');
                    $totalKirim = $order->items->sum('qty_terkirim');
                    $isLunas = $order->payment_status->value === 'paid';
                    $isTerkirimSemua = ($totalKirim > 0 && $totalKirim >= $totalPesan);
                @endphp

                @if($isLunas && $isTerkirimSemua && in_array($order->status->value, ['diproses', 'dikirim', 'selesai']))
                    <div class="flex flex-col w-full md:w-auto">
                        <span class="text-[10px] uppercase font-bold text-zinc-400 mb-1 tracking-wider">Status Akhir</span>
                        <span class="px-3 py-1.5 bg-green-100 text-green-800 rounded-lg text-xs md:text-sm font-bold shadow-sm border border-green-200 text-center">✔ Pesanan Selesai</span>
                    </div>
                @elseif(in_array($order->status->value, ['menunggu_approval', 'batal']))
                    <div class="flex flex-col w-full md:w-auto">
                        <span class="text-[10px] uppercase font-bold text-zinc-400 mb-1 tracking-wider">Status Pesanan</span>
                        @if($order->status->value === 'menunggu_approval')
                            <span class="px-3 py-1.5 bg-amber-100 text-amber-800 rounded-lg text-xs md:text-sm font-bold shadow-sm border border-amber-200 text-center">Menunggu Persetujuan</span>
                        @else
                            <span class="px-3 py-1.5 bg-red-100 text-red-800 rounded-lg text-xs md:text-sm font-bold shadow-sm border border-red-200 text-center">Dibatalkan</span>
                        @endif
                    </div>
                @else
                    <div class="grid grid-cols-3 md:flex gap-2 md:gap-3 w-full md:w-auto">
                        <div class="flex flex-col">
                            <span class="text-[9px] md:text-[10px] uppercase font-bold text-zinc-400 mb-1 tracking-wider">Pesanan</span>
                            <span class="px-2 py-1.5 bg-blue-100 text-blue-800 rounded-md text-[10px] md:text-xs font-bold border border-blue-200 text-center">{{ $order->status->label() }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[9px] md:text-[10px] uppercase font-bold text-zinc-400 mb-1 tracking-wider">Pembayaran</span>
                            @if($order->payment_status->value === 'paid')
                                <span class="px-2 py-1.5 bg-green-100 text-green-800 rounded-md text-[10px] md:text-xs font-bold border border-green-200 text-center">Lunas</span>
                            @elseif($order->payment_status->value === 'partial')
                                <span class="px-2 py-1.5 bg-amber-100 text-amber-800 rounded-md text-[10px] md:text-xs font-bold border border-amber-200 text-center">DP</span>
                            @else
                                <span class="px-2 py-1.5 bg-red-100 text-red-800 rounded-md text-[10px] md:text-xs font-bold border border-red-200 text-center">Belum Bayar</span>
                            @endif
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[9px] md:text-[10px] uppercase font-bold text-zinc-400 mb-1 tracking-wider">Pengiriman</span>
                            @if($totalKirim == 0)
                                <span class="px-2 py-1.5 bg-zinc-100 text-zinc-800 rounded-md text-[10px] md:text-xs font-bold border border-zinc-200 text-center">Belum Kirim</span>
                            @elseif($totalKirim < $totalPesan)
                                <span class="px-2 py-1.5 bg-indigo-100 text-indigo-800 rounded-md text-[10px] md:text-xs font-bold border border-indigo-200 text-center">Sebagian</span>
                            @else
                                <span class="px-2 py-1.5 bg-green-100 text-green-800 rounded-md text-[10px] md:text-xs font-bold border border-green-200 text-center">Terkirim</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 2. NAVIGASI TAB (Scrollable di HP) -->
    <div x-data="{ tab: 'rincian' }" class="mb-6">
        <div class="flex border-b border-zinc-200 dark:border-zinc-700 overflow-x-auto whitespace-nowrap">
            <button @click="tab = 'rincian'" :class="{ 'border-indigo-600 text-indigo-600': tab === 'rincian', 'border-transparent text-zinc-500': tab !== 'rincian' }" class="px-4 md:px-6 py-3 text-sm font-medium border-b-2 transition-colors">
                Rincian Pesanan
            </button>
            @if(in_array($order->status->value, ['dikonfirmasi', 'diproses', 'dikirim', 'selesai', 'batal']))
                <button @click="tab = 'pembayaran'" :class="{ 'border-indigo-600 text-indigo-600': tab === 'pembayaran', 'border-transparent text-zinc-500': tab !== 'pembayaran' }" class="px-4 md:px-6 py-3 text-sm font-medium border-b-2 transition-colors">
                    Pembayaran
                </button>
                <button @click="tab = 'pengiriman'" :class="{ 'border-indigo-600 text-indigo-600': tab === 'pengiriman', 'border-transparent text-zinc-500': tab !== 'pengiriman' }" class="px-4 md:px-6 py-3 text-sm font-medium border-b-2 transition-colors">
                    Pengiriman
                </button>
                @if($order->returns->count() > 0)
                <button @click="tab = 'retur'" :class="{ 'border-indigo-600 text-indigo-600': tab === 'retur', 'border-transparent text-zinc-500': tab !== 'retur' }" class="px-4 md:px-6 py-3 text-sm font-medium border-b-2 transition-colors">
                    Retur
                </button>
                @endif
            @endif
        </div>

        <!-- KONTEN TAB: RINCIAN -->
        <div x-show="tab === 'rincian'" class="mt-4 bg-white dark:bg-zinc-800 p-4 md:p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Rincian Barang</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700 text-xs md:text-sm text-zinc-500">
                            <th class="py-2">Produk</th>
                            <th class="py-2">Harga</th>
                            <th class="py-2 text-center">Qty</th>
                            <th class="py-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr class="border-b border-zinc-100 dark:border-zinc-700/50">
                                <td class="py-3 text-sm text-zinc-900 dark:text-white">{{ $item->nama_produk }}</td>
                                <td class="py-3 text-xs md:text-sm text-zinc-600 dark:text-zinc-400">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="py-3 text-xs md:text-sm text-zinc-600 dark:text-zinc-400 text-center">{{ $item->qty }}</td>
                                <td class="py-3 text-right font-medium text-zinc-900 dark:text-white text-sm">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6 border-t border-zinc-200 dark:border-zinc-700 pt-4 space-y-2 w-full md:w-1/2 md:ml-auto">
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500">Subtotal</span>
                    <span class="text-zinc-900 dark:text-white">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($order->diskon > 0)
                    <div class="flex justify-between text-sm text-green-600">
                        <span>Diskon {{ $order->diskon_tipe === 'persen' ? '('.$order->diskon.'%)' : '' }}</span>
                        <span>- Rp {{ number_format($order->diskon_tipe === 'persen' ? ($order->subtotal * $order->diskon / 100) : $order->diskon, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500">PPN</span>
                    <span class="text-zinc-900 dark:text-white">Rp {{ number_format($order->ppn, 0, ',', '.') }}</span>
                </div>
                @if($order->biaya_pengiriman > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">Biaya Pengiriman</span>
                        <span class="text-zinc-900 dark:text-white">Rp {{ number_format($order->biaya_pengiriman, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-bold text-lg pt-2 border-t border-zinc-100 dark:border-zinc-700">
                    <span class="text-zinc-900 dark:text-white">Grand Total</span>
                    <span class="text-indigo-600 dark:text-indigo-400">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($order->invoice)
                <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-200 dark:border-blue-800 flex flex-col md:flex-row justify-between items-center gap-3">
                    <p class="text-blue-800 dark:text-blue-300 font-medium text-sm">Invoice: <span class="font-bold font-mono">{{ $order->invoice->nomor_invoice }}</span></p>
                    <a href="javascript:void(0)" onclick="document.getElementById('invoice-modal').classList.remove('hidden');" class="w-full md:w-auto text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm">Lihat Invoice</a>
                </div>
            @endif

            @if(in_array($order->status->value, ['dikirim', 'selesai']) && auth()->user()->hasRole('customer'))
                <div class="mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700 text-right">
                    <button wire:click="openReturModal" class="w-full md:w-auto bg-amber-100 hover:bg-amber-200 text-amber-800 border border-amber-300 px-4 py-2.5 rounded-lg text-sm font-bold transition shadow-sm flex items-center justify-center gap-2 md:ml-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                        Ajukan Retur Barang
                    </button>
                </div>
            @endif
        </div>

        <!-- KONTEN TAB: PEMBAYARAN -->
        <div x-show="tab === 'pembayaran'" class="mt-4 space-y-4" style="display: none;">
            <!-- Ringkasan Pembayaran -->
            <div class="bg-white dark:bg-zinc-800 p-4 md:p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <h3 class="font-bold text-lg text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2">Ringkasan Pembayaran</h3>
                
                @php $sudahDibayar = $order->payments->where('status', 'verified')->sum('jumlah'); @endphp

                <div class="space-y-3 mb-6 bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-lg border border-zinc-100 dark:border-zinc-700">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-zinc-500 dark:text-zinc-400">Total Tagihan</span>
                        <span class="font-bold text-zinc-900 dark:text-white">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-zinc-500 dark:text-zinc-400">Sudah Dibayar</span>
                        <span class="font-bold text-green-600">Rp {{ number_format($sudahDibayar, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-zinc-200 dark:border-zinc-700">
                        <span class="font-bold text-zinc-700 dark:text-zinc-300">Sisa Tagihan</span>
                        <span class="font-bold text-xl text-red-600">Rp {{ number_format($order->sisaTagihan(), 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($order->status->value === 'menunggu_approval')
                    <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                        <p class="text-sm text-amber-800">Menunggu persetujuan admin sebelum pembayaran dapat dilakukan.</p>
                    </div>
                @elseif($order->status->value === 'batal')
                    <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-800">Pesanan ini telah dibatalkan.</p>
                    </div>
                @else
                    <!-- Form Upload -->
                    @if($order->sisaTagihan() > 0 && $order->payments->where('status', 'pending')->count() == 0)
                        <div class="mt-2">
                            <!-- INSTRUKSI TRANSFER -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg mb-5 border border-blue-200 dark:border-blue-800">
                                <p class="text-sm text-blue-800 dark:text-blue-300 font-medium mb-3">Silakan transfer ke salah satu rekening berikut:</p>
                                
                                @php
                                    $activeBanks = \App\Models\Setting::get('bank_accounts', []);
                                    $activeBanks = collect($activeBanks)->where('is_active', true);
                                @endphp

                                <div class="space-y-2">
                                    @foreach($activeBanks as $bank)
                                        <div class="flex justify-between items-center bg-white dark:bg-zinc-800 p-3 rounded-md border border-blue-100 dark:border-zinc-700">
                                            <div>
                                                <div class="font-bold text-zinc-900 dark:text-white text-sm">{{ $bank['bank_name'] }} - {{ $bank['account_number'] }}</div>
                                                <div class="text-xs text-zinc-500">a/n {{ $bank['holder'] }}</div>
                                            </div>
                                            <button type="button" onclick="navigator.clipboard.writeText('{{ $bank['account_number'] }}'); alert('Nomor rekening disalin!')" class="p-1.5 text-zinc-400 hover:text-indigo-600 transition" title="Salin">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <form wire:submit="uploadBukti" class="space-y-4">
                                <div>
                                    <div class="flex justify-between items-end mb-2">
                                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Nominal Transfer (Rp)</label>
                                        <div class="flex gap-1">
                                            <button type="button" wire:click="$set('nominal_bayar', {{ $order->sisaTagihan() }})" class="px-2 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-bold rounded border border-indigo-200 hover:bg-indigo-100 transition">Lunas</button>
                                            <button type="button" wire:click="$set('nominal_bayar', {{ $order->sisaTagihan() / 2 }})" class="px-2 py-1 bg-zinc-100 text-zinc-700 text-[10px] font-bold rounded border border-zinc-300 hover:bg-zinc-200 transition">50%</button>
                                        </div>
                                    </div>
                                    <input type="number" wire:model="nominal_bayar" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white font-bold">
                                    @error('nominal_bayar') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Upload Bukti Transfer</label>
                                    <div class="relative">
                                        <input type="file" wire:model="bukti_transfer" id="bukti_transfer" class="hidden" accept="image/*,.pdf">
                                        <label for="bukti_transfer" class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg cursor-pointer hover:bg-zinc-50 transition bg-white dark:bg-zinc-800">
                                            @if ($bukti_transfer)
                                                <svg class="w-5 h-5 text-green-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span class="text-xs font-bold text-green-600">File Terpilih</span>
                                            @else
                                                <svg class="w-5 h-5 text-zinc-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Pilih File Bukti Transfer</span>
                                            @endif
                                        </label>
                                    </div>
                                    @error('bukti_transfer') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                                    
                                    @if ($bukti_transfer)
                                        <div class="mt-3">
                                            <img src="{{ $bukti_transfer->temporaryUrl() }}" class="h-16 object-contain rounded-lg border border-zinc-200 shadow-sm">
                                        </div>
                                    @endif
                                </div>
                                
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg font-bold transition shadow-sm flex justify-center items-center">
                                    <span wire:loading.remove wire:target="uploadBukti">Kirim Bukti Pembayaran</span>
                                    <span wire:loading wire:target="uploadBukti" class="flex items-center gap-2">
                                        @include('partials.spinner') Mengunggah...
                                    </span>
                                </button>
                            </form>
                        </div>
                    @elseif($order->payments->where('status', 'pending')->count() > 0)
                        <div class="flex items-start gap-3 p-4 bg-blue-50 border border-blue-200 rounded-lg mt-4">
                            <p class="text-sm text-blue-800">Bukti transfer Anda sedang dalam proses verifikasi oleh tim Finance. Mohon tunggu.</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- KONTEN TAB: PENGIRIMAN -->
        <div x-show="tab === 'pengiriman'" class="mt-4 space-y-6" style="display: none;">
            <!-- 1. TABEL STATUS BARANG -->
            <div class="bg-white dark:bg-zinc-800 p-4 md:p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Status Pengiriman Barang</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-zinc-200 dark:border-zinc-700 text-xs md:text-sm text-zinc-500">
                                <th class="py-2">Nama Barang</th>
                                <th class="py-2 text-center">Qty</th>
                                <th class="py-2 text-center text-red-600">Belum Kirim</th>
                                <th class="py-2 text-center text-blue-600">Dikirim</th>
                                <th class="py-2 text-center text-green-600">Terkirim</th>
                                <th class="py-2 text-center">Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                @php 
                                    $qtyDikirim = $order->shipments->filter(fn($s) => $s->status->value === 'dikirim')->flatMap->items->where('order_item_id', $item->id)->sum('qty_dikirim');
                                    $qtyTerkirim = $order->shipments->filter(fn($s) => $s->status->value === 'diterima')->flatMap->items->where('order_item_id', $item->id)->sum('qty_dikirim');
                                    $sisa = $item->qty - $qtyDikirim - $qtyTerkirim;
                                @endphp
                                <tr class="border-b border-zinc-100 dark:border-zinc-700/50">
                                    <td class="py-2 text-xs md:text-sm text-zinc-900 dark:text-white">{{ $item->nama_produk }}</td>
                                    <td class="py-2 text-xs md:text-sm text-center font-medium">{{ $item->qty }}</td>
                                    <td class="py-2 text-xs md:text-sm text-center font-bold text-red-600">{{ $sisa > 0 ? $sisa : '-' }}</td>
                                    <td class="py-2 text-xs md:text-sm text-center font-bold text-blue-600">{{ $qtyDikirim > 0 ? $qtyDikirim : '-' }}</td>
                                    <td class="py-2 text-xs md:text-sm text-center font-bold text-green-600">{{ $qtyTerkirim > 0 ? $qtyTerkirim : '-' }}</td>
                                    <td class="py-2 text-xs md:text-sm text-center text-zinc-500">{{ $item->product->satuan ?? 'pcs' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. RIWAYAT PENGIRIMAN (ACCORDION) -->
            <div class="bg-white dark:bg-zinc-800 p-4 md:p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Riwayat Pengiriman</h3>
                
                @if($order->shipments->count() > 0)
                    <div class="space-y-3">
                        @foreach($order->shipments as $index => $shipment)
                            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden" x-data="{ open: false }">
                                <div @click="open = !open" class="p-3 flex justify-between items-center cursor-pointer bg-zinc-50 dark:bg-zinc-900/50 hover:bg-zinc-100 transition">
                                    <div class="flex items-center gap-2 md:gap-3">
                                        <div class="bg-indigo-100 text-indigo-700 font-bold w-7 h-7 md:w-8 md:h-8 flex items-center justify-center rounded-full text-xs md:text-sm">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-xs md:text-sm text-zinc-900 dark:text-white">{{ $shipment->ekspedisi }} - {{ $shipment->no_resi ?? 'Tanpa Resi' }}</div>
                                            <div class="text-[10px] md:text-xs text-zinc-500">Dikirim: {{ $shipment->shipped_at ? $shipment->shipped_at->format('d M Y') : '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 md:gap-3">
                                        <a href="{{ route('surat-jalan.download', $shipment->id) }}" target="_blank" @click.stop class="text-[10px] md:text-xs font-medium text-indigo-600 hover:underline">Lihat SJ</a>
                                        <svg :class="{'rotate-180': open}" class="w-4 h-4 text-zinc-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>

                                <div x-show="open" x-collapse style="display: none;" class="border-t border-zinc-200 dark:border-zinc-700 p-3 md:p-4 bg-zinc-50/50 dark:bg-zinc-900/20">
                                    <table class="w-full text-left border-collapse mb-4">
                                        <thead>
                                            <tr class="border-b border-zinc-200 dark:border-zinc-700 text-[10px] md:text-xs text-zinc-500 uppercase">
                                                <th class="pb-2">Nama Barang</th>
                                                <th class="pb-2 text-center">Qty</th>
                                                <th class="pb-2 text-center">Satuan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                                            @foreach($shipment->items as $item)
                                                <tr class="hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                                                    <td class="py-2 text-xs md:text-sm text-zinc-900 dark:text-white">{{ $item->orderItem->nama_produk }}</td>
                                                    <td class="py-2 text-xs md:text-sm text-center font-bold text-indigo-600">{{ $item->qty_dikirim }}</td>
                                                    <td class="py-2 text-xs md:text-sm text-center text-zinc-500">{{ $item->orderItem->product->satuan ?? 'pcs' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <div class="pt-3 border-t border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                                        @if($shipment->status->value === 'diterima')
                                            <span class="text-xs md:text-sm font-bold text-green-600">✔ Diterima: {{ $shipment->received_at ? $shipment->received_at->format('d M Y') : '-' }}</span>
                                        @else
                                            <span class="text-xs md:text-sm font-bold text-amber-600">🚚 Di perjalanan</span>
                                            <button wire:click="konfirmasiDiterima({{ $shipment->id }})" wire:confirm="Konfirmasi bahwa barang sudah Anda terima?" class="bg-green-600 hover:bg-green-700 text-white px-2 md:px-3 py-1.5 rounded text-[10px] md:text-xs font-bold transition flex justify-center items-center min-w-[100px]">
                                                <span wire:loading.remove wire:target="konfirmasiDiterima({{ $shipment->id }})">Terima Barang</span>
                                                <span wire:loading wire:target="konfirmasiDiterima({{ $shipment->id }})" class="flex items-center gap-1">
                                                    @include('partials.spinner')
                                                </span>
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

        <!-- KONTEN TAB: RETUR -->
        <div x-show="tab === 'retur'" class="mt-6 space-y-6" style="display: none;">
            @if($order->returns->count() > 0)
                <div class="mt-6 bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                    <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Riwayat Retur</h3>
                    
                    <div class="space-y-3">
                        @foreach($order->returns as $index => $ret)
                            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden" x-data="{ open: false }">
                                
                            <!-- Header Card -->
                                <div @click="open = !open" class="p-3 flex justify-between items-center cursor-pointer bg-zinc-50 dark:bg-zinc-900/50 hover:bg-zinc-100 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-amber-100 text-amber-700 font-bold w-8 h-8 flex items-center justify-center rounded-full text-xs">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <!-- Menampilkan Kode Retur -->
                                            <div class="font-bold text-sm text-zinc-900 dark:text-white font-mono">
                                                {{ $ret->kode_retur }}
                                            </div>
                                            <div class="text-[10px] text-zinc-500 mt-1">
                                                Diajukan: {{ $ret->created_at->format('d M Y') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @if($ret->status->value === 'diajukan')
                                            <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-800 rounded-full">Menunggu</span>
                                        @elseif($ret->status->value === 'disetujui')
                                            <span class="px-2 py-0.5 text-[10px] font-bold bg-blue-100 text-blue-800 rounded-full">Diproses</span>
                                        @elseif($ret->status->value === 'selesai')
                                            <span class="px-2 py-0.5 text-[10px] font-bold bg-green-100 text-green-800 rounded-full">Selesai</span>
                                        @else
                                            <span class="px-2 py-0.5 text-[10px] font-bold bg-red-100 text-red-800 rounded-full">Ditolak</span>
                                        @endif
                                        <svg :class="{'rotate-180': open}" class="w-4 h-4 text-zinc-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Isi Detail -->
                                <div x-show="open" x-collapse style="display: none;" class="border-t border-zinc-200 dark:border-zinc-700 p-4 bg-zinc-50/50 dark:bg-zinc-900/20">
                                    <div class="grid grid-cols-2 gap-4 mb-3">
                                        <div>
                                            <div class="text-xs text-zinc-500 mb-1">Kategori Alasan</div>
                                            <div class="text-sm font-medium text-zinc-900 dark:text-white capitalize">{{ str_replace('_', ' ', $ret->alasan_retur) }}</div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-zinc-500 mb-1">Status</div>
                                            <div class="text-sm font-bold text-zinc-900 dark:text-white">{{ $ret->status->label() }}</div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-xs text-zinc-500 mb-1">Catatan Anda</div>
                                        <div class="text-sm text-zinc-700 dark:text-zinc-300">{{ $ret->alasan ?: '-' }}</div>
                                    </div>
                                    
                                    @if($ret->refund_amount > 0)
                                        <div class="pt-3 border-t border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                                            <span class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Nominal Refund</span>
                                            <span class="text-base font-bold text-green-600">Rp {{ number_format($ret->refund_amount, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="text-xs text-zinc-500 text-right mt-1">Metode: {{ $ret->refund_method === 'saldo' ? 'Saldo Akun' : 'Transfer Manual' }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL INVOICE (Sama seperti sebelumnya) -->
    <div id="invoice-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-2xl w-full max-w-4xl relative my-8">
            <button onclick="document.getElementById('invoice-modal').classList.add('hidden');" class="absolute top-4 right-4 text-zinc-500 hover:text-red-500 bg-zinc-100 hover:bg-red-50 p-2 rounded-full transition z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div class="p-4 md:p-8 text-zinc-900 dark:text-zinc-200 overflow-x-auto">
                @include('pdf.partials.invoice-content', ['order' => $order])
            </div>
        </div>
    </div>

    <!-- MODAL RETUR -->
    @if($isReturModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Ajukan Retur Barang</h3>
                    <button wire:click="$set('isReturModalOpen', false)" class="text-zinc-400 hover:text-zinc-600 text-2xl">&times;</button>
                </div>
                <form wire:submit="ajukanRetur" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Pilih Barang & Qty yang Diretur</label>
                        @foreach($order->items as $item)
                            <div class="flex justify-between items-center py-2 border-b border-zinc-100 dark:border-zinc-700/50">
                                <span class="text-sm">{{ $item->product->nama ?? 'Produk Dihapus' }} (Beli: {{ $item->qty }})</span>
                                <input wire:model="retur_qty.{{ $item->id }}" type="number" max="{{ $item->qty }}" min="0" class="w-20 px-2 py-1 text-center border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                            </div>
                        @endforeach
                        @error('retur_qty') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Kategori Alasan</label>
                        <select wire:model="retur_kategori" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                            <option value="rusak">Barang Rusak</option>
                            <option value="salah_kirim">Salah Kirim</option>
                            <option value="tidak_sesuai">Tidak Sesuai Deskripsi</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Detail Alasan</label>
                        <textarea wire:model="retur_alasan" rows="2" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600" placeholder="Jelaskan detail kerusakan..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <button type="button" wire:click="$set('isReturModalOpen', false)" class="px-4 py-2 text-sm font-medium text-zinc-700 bg-zinc-100 hover:bg-zinc-200 rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-sm">Kirim Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>