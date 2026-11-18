<div>
    <!-- LOGIKA TOMBOL KEMBALI BERDASARKAN ROLE -->
        @role('finance')
            <a href="{{ route('payments.verify') }}" class="text-indigo-600 hover:underline text-sm font-medium mb-2 inline-block">&larr; Kembali ke Verifikasi Pembayaran</a>
        @endrole

        @role('warehouse')
            <a href="{{ route('warehouse.shipments') }}" class="text-indigo-600 hover:underline text-sm font-medium mb-2 inline-block">&larr; Kembali ke Antrean Pengiriman</a>
        @endrole

        @role('superadmin|admin|sales')
            <a href="{{ route('admin.orders') }}" class="text-indigo-600 hover:underline text-sm font-medium mb-2 inline-block">&larr; Kembali ke Daftar Pesanan</a>
        @endrole
        
    <!-- 1. HEADER RINGKASAN -->
    <div class="mb-6 bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            
            <div>
                <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Nomor Pesanan</div>
                <h1 class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">
                    {{ $order->kode_order }}
                </h1>
                
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-zinc-700 dark:text-zinc-300">
                    <span>Dibuat: <strong class="font-bold">{{ $order->created_at->translatedFormat('d F Y') }}</strong> pukul <strong class="font-bold">{{ $order->created_at->format('H:i') }}</strong></span>
                    @if($order->sales && $order->sales->name !== 'Sistem')
                        <span class="hidden md:inline text-zinc-300">|</span>
                        <span>Sales PIC: <strong class="font-bold">{{ $order->sales->name }}</strong></span>
                    @endif
                </div>
            </div>

            <!-- Ringkasan Status & Tombol Aksi Admin -->
            <div class="flex flex-wrap items-center gap-4">
                
                @php
                    $totalPesan = $order->items->sum('qty');
                    $totalKirim = $order->items->sum('qty_terkirim');
                    $isLunas = $order->payment_status->value === 'paid';
                    $isTerkirimSemua = ($totalKirim > 0 && $totalKirim >= $totalPesan);
                @endphp

                <!-- TOMBOL INVOICE (Hanya muncul jika invoice sudah dibuat) -->
                @if($order->invoice)
                    <div class="flex flex-col items-end">
                        <span class="text-[10px] uppercase font-bold text-zinc-400 mb-1 tracking-wider">Dokumen</span>
                        <a href="javascript:void(0)" 
                            onclick="event.preventDefault(); document.getElementById('invoice-modal-admin').classList.remove('hidden');"
                            class="bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                Lihat Invoice
                        </a>
                    </div>
                @endif

                <!-- Tombol Approve & Diskon (Jika Menunggu) -->
                @if($order->status->value === 'menunggu_approval')
                    <div class="flex flex-col items-end gap-2">
                        <span class="text-[10px] uppercase font-bold text-zinc-400 mb-1 tracking-wider">Aksi Diperlukan</span>
                        
                        <!-- Tombol Diskon -->
                        @if(auth()->user()->hasAnyRole(['superadmin', 'admin', 'finance']))
                            <button wire:click="openDiskonModal" class="bg-amber-100 hover:bg-amber-200 text-amber-800 border border-amber-300 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition w-full">
                                Beri Diskon
                            </button>
                        @endif

                        <button wire:click="approve" wire:confirm="Setujui pesanan ini?" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition w-full flex justify-center items-center">
                            <span wire:loading.remove wire:target="approve">Setujui Pesanan</span>
                            <span wire:loading wire:target="approve" class="flex items-center gap-2">
                                @include('partials.spinner') Menyetujui...
                            </span>
                        </button>
                    </div>
                    @if(in_array($order->status->value, ['menunggu_approval', 'dikonfirmasi', 'diproses']))
                    <div class="flex flex-col items-end gap-2">
                        <span class="text-[10px] uppercase font-bold text-zinc-400 mb-1 tracking-wider">Logistik</span>
                        <button wire:click="openOngkirModal" class="bg-zinc-100 hover:bg-zinc-200 text-zinc-800 border border-zinc-300 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition w-full">
                            Edit Ongkir
                        </button>
                    </div>
                    @endif
                @endif

                <!-- Status Badges -->
                @if($isLunas && $isTerkirimSemua && in_array($order->status->value, ['diproses', 'dikirim', 'selesai']))
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-zinc-400 mb-1 tracking-wider">Status Akhir</span>
                        <span class="px-4 py-2 bg-green-100 text-green-800 rounded-lg text-sm font-bold shadow-sm border border-green-200">✔ Pesanan Selesai</span>
                    </div>
                @elseif(in_array($order->status->value, ['menunggu_approval', 'batal']))
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-zinc-400 mb-1 tracking-wider">Status Pesanan</span>
                        @if($order->status->value === 'menunggu_approval')
                            <span class="px-4 py-2 bg-amber-100 text-amber-800 rounded-lg text-sm font-bold shadow-sm border border-amber-200">Menunggu Persetujuan</span>
                        @else
                            <span class="px-4 py-2 bg-red-100 text-red-800 rounded-lg text-sm font-bold shadow-sm border border-red-200">Dibatalkan</span>
                        @endif
                    </div>
                @else
                    <!-- Jika Sedang Berjalan -->
                    <div class="flex gap-3">
                        <!-- ... (biarkan kode status Pesanan, Pembayaran, Pengiriman yang sudah ada) ... -->
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase font-bold text-zinc-400 mb-1 tracking-wider">Pesanan</span>
                            <span class="px-3 py-1.5 bg-blue-100 text-blue-800 rounded-md text-xs font-bold border border-blue-200 text-center">{{ $order->status->label() }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase font-bold text-zinc-400 mb-1 tracking-wider">Pembayaran</span>
                            @if($order->payment_status->value === 'paid')
                                <span class="px-3 py-1.5 bg-green-100 text-green-800 rounded-md text-xs font-bold border border-green-200 text-center">Lunas</span>
                            @elseif($order->payment_status->value === 'partial')
                                <span class="px-3 py-1.5 bg-amber-100 text-amber-800 rounded-md text-xs font-bold border border-amber-200 text-center">DP</span>
                            @else
                                <span class="px-3 py-1.5 bg-red-100 text-red-800 rounded-md text-xs font-bold border border-red-200 text-center">Belum Bayar</span>
                            @endif
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase font-bold text-zinc-400 mb-1 tracking-wider">Pengiriman</span>
                            @if($totalKirim == 0)
                                <span class="px-3 py-1.5 bg-zinc-100 text-zinc-800 rounded-md text-xs font-bold border border-zinc-200 text-center">Belum Kirim</span>
                            @elseif($totalKirim < $totalPesan)
                                <span class="px-3 py-1.5 bg-indigo-100 text-indigo-800 rounded-md text-xs font-bold border border-indigo-200 text-center">Sebagian</span>
                            @else
                                <span class="px-3 py-1.5 bg-green-100 text-green-800 rounded-md text-xs font-bold border border-green-200 text-center">Terkirim</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 2. INFORMASI CUSTOMER & PEMBAYARAN (Grid 2 Kolom, Tanpa Titik Dua) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <h3 class="font-bold text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2">Informasi Customer</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="text-xs text-zinc-500 mb-1">Nama Customer</div>
                    <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $order->customer->nama_lengkap }}</div>
                </div>
                <div>
                    <div class="text-xs text-zinc-500 mb-1">Perusahaan</div>
                    <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $order->customer->nama_perusahaan ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-zinc-500 mb-1">Telepon</div>
                    <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $order->customer->telepon }}</div>
                </div>
                <div class="col-span-2">
                    <div class="text-xs text-zinc-500 mb-1">Alamat</div>
                    <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $order->customer->alamat ?? '-' }}</div>
                </div>
            </div>
        </div>

        @role('superadmin|admin|finance|sales')
        <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <h3 class="font-bold text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2">Informasi Pembayaran</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="text-xs text-zinc-500 mb-1">Total Tagihan</div>
                    <div class="text-sm font-medium text-zinc-900 dark:text-white">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                </div>
                <div>
                    <div class="text-xs text-zinc-500 mb-1">Status Pembayaran</div>
                    <div class="text-sm font-bold {{ $order->payment_status->value === 'paid' ? 'text-green-600' : 'text-amber-600' }}">
                        {{ $order->payment_status->label() }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-zinc-500 mb-1">Telah Dibayar</div>
                    <div class="text-sm font-bold text-green-600">Rp {{ number_format($order->total - $order->sisaTagihan(), 0, ',', '.') }}</div>
                </div>
                <div>
                    <div class="text-xs text-zinc-500 mb-1">Sisa Tagihan</div>
                    <div class="text-sm font-bold text-red-600">Rp {{ number_format($order->sisaTagihan(), 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        @endrole
    </div>

    <!-- 3. RINCIAN BARANG -->
    <div class="mb-6 bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <h3 class="font-bold text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2">Rincian Barang</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700 text-sm text-zinc-500">
                        <th class="py-2">Produk</th>
                        <th class="py-2 text-center">Qty</th>
                        <th class="py-2 text-center text-red-600">Belum Kirim</th>
                        <th class="py-2 text-center text-blue-600">Dikirim</th>
                        <th class="py-2 text-center text-green-600">Terkirim</th>
                        <th class="py-2 text-center">Satuan</th>
                        <th class="py-2 text-right">Total</th>
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

                            // Sisa belum dikirim
                            $sisa = $item->qty - $qtyDikirim - $qtyTerkirim;
                        @endphp
                        <tr class="border-b border-zinc-100 dark:border-zinc-700/50">
                            <td class="py-3 text-sm text-zinc-900 dark:text-white">{{ $item->nama_produk }}</td>
                            <td class="py-3 text-sm text-center font-medium">{{ $item->qty }}</td>
                            <td class="py-3 text-sm text-center font-bold text-red-600">{{ $sisa > 0 ? $sisa : '-' }}</td>
                            <td class="py-3 text-sm text-center font-bold text-blue-600">{{ $qtyDikirim > 0 ? $qtyDikirim : '-' }}</td>
                            <td class="py-3 text-sm text-center font-bold text-green-600">{{ $qtyTerkirim > 0 ? $qtyTerkirim : '-' }}</td>
                            <td class="py-3 text-sm text-center text-zinc-500">{{ $item->product->satuan ?? 'pcs' }}</td>
                            <td class="py-3 text-right text-sm font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6 pt-4 space-y-2 w-1/2 ml-auto">
                <div class="flex justify-between font-bold text-lg">
                    <span class="text-zinc-500">Subtotal</span>
                    <span class="text-zinc-900 dark:text-white">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500">Diskon</span>
                    <span class="text-zinc-900 dark:text-white">{{ number_format($order->diskon, 0, ',', '.') }}%</span>
                    <span class="text-zinc-900 dark:text-white">- Rp {{ number_format($order->diskon * $order->subtotal / 100, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500">PPN</span>
                    <span class="text-zinc-900 dark:text-white">Rp {{ number_format($order->ppn, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500">Biaya Pengiriman</span>
                    <span class="text-zinc-900 dark:text-white">Rp {{ number_format($order->biaya_pengiriman, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-lg pt-2 border-t border-zinc-100 dark:border-zinc-700">
                    <span class="text-zinc-900 dark:text-white">Grand Total</span>
                    <span class="text-indigo-600 dark:text-indigo-400">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
        </div>
        <!-- TOMBOL SHORTCUT PENGIRIMAN -->
        <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-700/50 flex justify-end">
            @php
                $adaSisa = $order->items->contains(fn($item) => ($item->qty - $item->qty_terkirim) > 0);
            @endphp

            @if($adaSisa && in_array($order->status->value, ['dikonfirmasi', 'diproses']))
                <!-- Tombol ini sekarang memanggil openShipModal, bukan redirect link -->
                <button wire:click="openShipModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    Proses Pengiriman Sekarang
                </button>
            @elseif(!$adaSisa)
                <span class="text-sm text-green-600 font-bold flex items-center gap-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Semua barang sudah terkirim
                </span>
            @endif
        </div>
    </div>

    <!-- 4. RIWAYAT PEMBAYARAN -->
    @role('superadmin|admin|finance|sales')
    <div class="mb-6 bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <h3 class="font-bold text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2">Riwayat Pembayaran</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700 text-sm text-zinc-500">
                        <th class="py-2">Tanggal</th>
                        <th class="py-2">Nominal</th>
                        <th class="py-2">Bukti</th>
                        <th class="py-2">Status</th>
                        <th class="py-2">Verifikator</th>
                        <th class="py-2 text-right">Aksi</th> <!-- Kolom Baru -->
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->payments as $payment)
                        <tr class="border-b border-zinc-100 dark:border-zinc-700/50">
                            <td class="py-3 text-sm">{{ $payment->created_at->format('d M Y H:i') }}</td>
                            <td class="py-3 text-sm font-medium">Rp {{ number_format($payment->jumlah, 0, ',', '.') }}</td>
                            <td class="py-3 text-sm">
                                <a href="{{ asset('storage/'.$payment->bukti_transfer) }}" target="_blank" class="text-indigo-600 underline">Lihat</a>
                            </td>
                            <td class="py-3 text-sm">
                               @if($payment->status->value === 'pending')
                                    <button wire:click="verifyPayment({{ $payment->id }})" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-bold transition shadow-sm mr-1 flex justify-center items-center min-w-[80px]">
                                        <span wire:loading.remove wire:target="verifyPayment({{ $payment->id }})">Verifikasi</span>
                                        <span wire:loading wire:target="verifyPayment({{ $payment->id }})" class="flex items-center gap-1">
                                            @include('partials.spinner')
                                        </span>
                                    </button>
                                    
                                    <button x-data x-on:click.prevent="if(confirm('Tolak pembayaran ini?')) $wire.rejectPayment({{ $payment->id }})" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-bold transition shadow-sm flex justify-center items-center min-w-[60px]">
                                        <span wire:loading.remove wire:target="rejectPayment({{ $payment->id }})">Tolak</span>
                                        <span wire:loading wire:target="rejectPayment({{ $payment->id }})" class="flex items-center gap-1">
                                            @include('partials.spinner')
                                        </span>
                                    </button>
                                @else
                                    <span class="text-xs text-zinc-400 italic">Selesai</span>
                                @endif
                            </td>
                            <td class="py-3 text-sm">{{ $payment->verifier->name ?? '-' }}</td>
                            
                            <!-- TOMBOL AKSI VERIFIKASI -->
                            <td class="py-3 text-right">
                                @if($payment->status->value === 'pending')
                                    <button wire:click="verifyPayment({{ $payment->id }})" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-bold transition shadow-sm mr-1">
                                        Verifikasi
                                    </button>
                                    <button x-data x-on:click.prevent="if(confirm('Tolak pembayaran ini?')) $wire.rejectPayment({{ $payment->id }})" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-bold transition shadow-sm">
                                        Tolak
                                    </button>
                                @else
                                    <span class="text-xs text-zinc-400 italic">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-4 text-center text-sm text-zinc-500">Belum ada pembayaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endrole

    <!-- 5. RIWAYAT PENGIRIMAN -->
    <div class="mb-6 bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <h3 class="font-bold text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2">Riwayat Pengiriman</h3>
        
        @if($order->shipments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700 text-sm text-zinc-500">
                            <th class="py-2">No. Resi / Ekspedisi</th>
                            <th class="py-2">Tgl Kirim</th>
                            <th class="py-2">Tgl Diterima</th>
                            <th class="py-2">Dikirim Oleh</th>
                            <th class="py-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->shipments as $shipment)
                            <tr class="border-b border-zinc-100 dark:border-zinc-700/50">
                                <td class="py-3 text-sm">
                                    <div class="font-bold text-zinc-900 dark:text-white">{{ $shipment->ekspedisi }}</div>
                                    <div class="text-xs text-indigo-600 font-mono">{{ $shipment->no_resi ?? 'Tanpa Resi' }}</div>
                                </td>
                                <td class="py-3 text-sm">
                                    {{ $shipment->shipped_at ? $shipment->shipped_at->format('d M Y H:i') : '-' }}
                                </td>
                                <td class="py-3 text-sm">
                                    @if($shipment->status->value === 'diterima')
                                        <span class="text-green-600 font-bold">{{ $shipment->received_at ? $shipment->received_at->format('d M Y H:i') : '-' }}</span>
                                    @else
                                        <span class="text-amber-500 italic">Belum Diterima</span>
                                    @endif
                                </td>
                                <td class="py-3 text-sm">
                                    {{ $shipment->user->name ?? 'Sistem' }}
                                </td>
                                <td class="py-3 text-right">
                                    @if($order->shipments->count() > 0)
                                        @php $lastShipment = $order->shipments->sortByDesc('id')->first(); @endphp
                                        <button onclick="event.stopPropagation(); @this.openEditResiModal({{ $lastShipment->id }})" class="bg-amber-100 hover:bg-amber-200 text-amber-800 border border-amber-300 px-3 py-1.5 rounded text-sm font-bold shadow-sm transition mr-2">
                                            Edit Resi
                                        </button>
                                    @endif

                                    <a href="{{ route('surat-jalan.download', $shipment->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Lihat Surat Jalan</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-zinc-500">Belum ada riwayat pengiriman untuk pesanan ini.</p>
        @endif
    </div>

    <!-- 6. RIWAYAT RETUR -->
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

    <!-- MODAL PENGIRIMAN -->
    @if($isShipModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Proses Pengiriman Barang</h3>
                    <button wire:click="$set('isShipModalOpen', false)" class="text-zinc-400 hover:text-zinc-600">&times;</button>
                </div>

                <form wire:submit="processShipment" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Ekspedisi *</label>
                            <input wire:model="ekspedisi" type="text" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">No. Resi</label>
                            <input wire:model="no_resi" type="text" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                        </div>
                    </div>

                    <div class="mt-4 border-t border-zinc-200 dark:border-zinc-700 pt-4">
                        <h4 class="font-medium text-zinc-900 dark:text-white mb-3">Daftar Barang</h4>
                        @foreach($order->items as $item)
                            @php $sisa = $item->qty - $item->qty_terkirim; @endphp
                            @if($sisa > 0)
                                <div class="flex justify-between items-center py-2 border-b border-zinc-100 dark:border-zinc-700/50">
                                    <div>
                                        <div class="font-medium text-sm text-zinc-900 dark:text-white">{{ $item->nama_produk }}</div>
                                        <div class="text-xs text-zinc-500">Sisa pesanan: {{ $sisa }}</div>
                                    </div>
                                    <div class="w-24">
                                        <input wire:model="qty_kirim.{{ $item->id }}" type="number" max="{{ $sisa }}" min="0" class="w-full px-3 py-1.5 text-center border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        @error('qty_kirim') <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <button type="button" wire:click="$set('isShipModalOpen', false)" class="px-4 py-2 text-sm font-medium text-zinc-700 bg-zinc-100 hover:bg-zinc-200 rounded-lg">Batal</button>
                        <button type="submit" 
                                x-data 
                                x-on:click.prevent="
                                    if (confirm('Apakah Anda yakin ingin memproses pengiriman ini? Stok akan otomatis berkurang.')) {
                                        $wire.processShipment();
                                    }
                                " 
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm">
                            Konfirmasi Pengiriman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL DISKON -->
    @if($isDiskonModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Beri Diskon Pesanan</h3>
                    <button wire:click="$set('isDiskonModalOpen', false)" class="text-zinc-400 hover:text-zinc-600 text-2xl">&times;</button>
                </div>
                <form wire:submit="simpanDiskon" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tipe Diskon</label>
                        <select wire:model="input_diskon_tipe" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                            <option value="nominal">Nominal (Rp)</option>
                            <option value="persen">Persentase (%)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                            Nilai Diskon {{ $input_diskon_tipe === 'persen' ? '(%)' : '(Rp)' }}
                        </label>
                        <input wire:model="input_diskon" type="number" step="0.01" min="0" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white font-bold">
                        @error('input_diskon') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <button type="button" wire:click="$set('isDiskonModalOpen', false)" class="px-4 py-2 text-sm font-medium text-zinc-700 bg-zinc-100 hover:bg-zinc-200 rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm flex items-center justify-center">
                            <span wire:loading.remove wire:target="simpanDiskon">Terapkan Diskon</span>
                            <span wire:loading wire:target="simpanDiskon" class="flex items-center gap-2">
                                @include('partials.spinner') Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL INVOICE ADMIN -->
    <div id="invoice-modal-admin" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-2xl w-full max-w-4xl relative my-8">
            
            <!-- Tombol Close -->
            <button onclick="document.getElementById('invoice-modal-admin').classList.add('hidden');" class="absolute top-4 right-4 text-zinc-500 hover:text-red-500 bg-zinc-100 hover:bg-red-50 p-2 rounded-full transition z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <!-- Isi Invoice -->
            <div class="p-8 md:p-12 text-zinc-900 dark:text-zinc-200">
                @include('pdf.partials.invoice-content', ['order' => $order])
            </div>
            
            <!-- Tombol Unduh PDF di dalam Modal Admin -->
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50 flex justify-end rounded-b-xl">
                <a href="{{ route('invoice.download', $order->id) }}" target="_blank" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition">
                    Unduh PDF
                </a>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT ONGKIR -->
    @if($isOngkirModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Edit Pengiriman</h3>
                    <button wire:click="$set('isOngkirModalOpen', false)" class="text-zinc-400 hover:text-zinc-600 text-2xl">&times;</button>
                </div>
                <form wire:submit="updateOngkir" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Metode Pengiriman</label>
                        <select wire:model.live="edit_metode_pengiriman" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                            <option value="ekspedisi">Ekspedisi / Kurir</option>
                            <option value="ambil_sendiri">Ambil Sendiri</option>
                        </select>
                    </div>
                    
                    @if($edit_metode_pengiriman === 'ekspedisi')
                        <div>
                            <label class="block text-sm font-medium mb-1">Biaya Pengiriman (Rp)</label>
                            <input wire:model="edit_biaya_pengiriman" type="number" min="0" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                            @error('edit_biaya_pengiriman') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <button type="button" wire:click="$set('isOngkirModalOpen', false)" class="px-4 py-2 text-sm font-medium text-zinc-700 bg-zinc-100 hover:bg-zinc-200 rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>