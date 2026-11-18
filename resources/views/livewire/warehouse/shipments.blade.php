<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Pengiriman</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Proses pengiriman pesanan yang sudah lunas/dikonfirmasi.</p>
        </div>
    </div>

    <!-- TOOLBAR FILTER -->
    <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-xs font-medium text-zinc-500 mb-1">Pencarian</label>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari No. Order atau Nama Customer..." class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div class="w-full md:w-56">
                <label class="block text-xs font-medium text-zinc-500 mb-1">Status Pengiriman</label>
                <select wire:model.live="filterShipStatus" class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="semua">Semua Pengiriman</option>
                    <option value="belum_kirim">Belum Dikirim</option>
                    <option value="dalam_pengiriman">Dalam Pengiriman</option>
                    <option value="sebagian">Dikirim Sebagian</option>
                    <option value="terkirim_semua">Terkirim Semua</option>
                </select>
            </div>

            <div class="w-full md:w-48">
                <label class="block text-xs font-medium text-zinc-500 mb-1">Periode</label>
                <select wire:model.live="filterPeriode" class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="semua">Semua Waktu</option>
                    <option value="hari_ini">Hari Ini</option>
                    <option value="bulan_ini">Bulan Ini</option>
                    <option value="tahun_ini">Tahun Ini</option>
                </select>
            </div>
        </div>
    </div>

        <!-- ========================================== -->
    <!-- TAMPILAN DESKTOP (TABEL) -->
    <!-- ========================================== -->
    <div class="hidden md:block bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700 text-sm font-medium text-zinc-500 dark:text-zinc-400">
                        <th class="px-6 py-4">No. Order & Tanggal</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4 text-center">Status Pengiriman</th>
                        <th class="px-6 py-4 text-center">Pengiriman Ke-</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($orders as $order)
                        @php
                            $totalPesan = $order->items->sum('qty');
                            $totalDalamPengiriman = $order->shipments->filter(fn($s) => $s->status->value === 'dikirim')->flatMap->items->sum('qty_dikirim');
                            $totalDiterima = $order->shipments->filter(fn($s) => $s->status->value === 'diterima')->flatMap->items->sum('qty_dikirim');
                            $sisaBelumKirim = $totalPesan - $totalDalamPengiriman - $totalDiterima;
                            $isSelesai = ($totalDiterima >= $totalPesan && $totalPesan > 0);
                        @endphp
                        <tr x-data="{ expanded: false }" class="hover:bg-indigo-50/50 transition">
                            <td class="px-6 py-4">
                                <button @click="expanded = !expanded" class="text-zinc-400 hover:text-indigo-600 mr-2 focus:outline-none">
                                    <svg :class="{'rotate-180': expanded}" class="w-5 h-5 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <span class="font-mono font-medium text-indigo-600 dark:text-indigo-400">{{ $order->kode_order }}</span>
                                <div class="text-xs text-zinc-500 ml-7">{{ $order->created_at->format('d M Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $order->customer->nama_lengkap }}</div>
                                <div class="text-xs text-zinc-500">{{ $order->customer->alamat ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($isSelesai)
                                    <span class="px-2.5 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full border border-green-200">Terkirim Semua</span>
                                @elseif($totalDalamPengiriman > 0 && $sisaBelumKirim == 0)
                                    <span class="px-2.5 py-1 text-xs font-bold bg-blue-100 text-blue-800 rounded-full border border-blue-200">Dalam Pengiriman</span>
                                @elseif($totalDalamPengiriman > 0 || $totalDiterima > 0)
                                    <span class="px-2.5 py-1 text-xs font-bold bg-amber-100 text-amber-800 rounded-full border border-amber-200">Dikirim Sebagian</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold bg-red-100 text-red-800 rounded-full border border-red-200">Belum Dikirim</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($order->shipments->count() > 0)
                                    <span class="font-bold text-zinc-900 dark:text-white">{{ $order->shipments->count() }}</span>
                                    <span class="text-xs text-zinc-500">/ kali</span>
                                @else
                                    <span class="text-zinc-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($order->shipments->count() > 0)
                                    @php $lastShipment = $order->shipments->sortByDesc('id')->first(); @endphp
                                    <button onclick="event.stopPropagation(); @this.openEditResiModal({{ $lastShipment->id }})" class="bg-amber-100 hover:bg-amber-200 text-amber-800 border border-amber-300 px-3 py-1.5 rounded text-sm font-bold shadow-sm transition mr-2">Edit Resi</button>
                                @endif
                                @if(!$isSelesai)
                                    <button onclick="event.stopPropagation(); @this.openShipModal({{ $order->id }})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-sm font-bold shadow-sm transition">Proses Kirim</button>
                                @endif
                            </td>
                        </tr>
                        
                        <!-- Accordion (Desktop) -->
                        <tr x-show="expanded" x-collapse style="display: none;" class="bg-zinc-50/50 dark:bg-zinc-900/20">
                            <td colspan="5" class="px-6 py-4 border-l-4 border-indigo-500">
                                <!-- ... (kode tabel riwayat pengiriman) ... -->
                                <h4 class="font-bold text-sm text-zinc-700 dark:text-zinc-300 mb-3">Riwayat Pengiriman</h4>
                                @if($order->shipments->count() > 0)
                                    <table class="w-full text-left bg-white dark:bg-zinc-800 rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-700">
                                        <thead>
                                            <tr class="bg-zinc-100 dark:bg-zinc-900/50 text-xs text-zinc-500">
                                                <th class="px-4 py-2">Ekspedisi / Resi</th>
                                                <th class="px-4 py-2">Tgl Kirim</th>
                                                <th class="px-4 py-2">Status</th>
                                                <th class="px-4 py-2 text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->shipments as $shipment)
                                                <tr class="border-t border-zinc-100 dark:border-zinc-700/50">
                                                    <td class="px-4 py-2 text-sm">
                                                        <div class="font-bold text-zinc-900 dark:text-white">{{ $shipment->ekspedisi }}</div>
                                                        <div class="text-xs text-indigo-600 font-mono">{{ $shipment->no_resi ?? 'Tanpa Resi' }}</div>
                                                    </td>
                                                    <td class="px-4 py-2 text-sm text-zinc-600">{{ $shipment->shipped_at ? $shipment->shipped_at->format('d M Y') : '-' }}</td>
                                                    <td class="px-4 py-2 text-sm">
                                                        @if($shipment->status->value === 'diterima')
                                                            <span class="text-green-600 font-bold">Diterima</span>
                                                        @else
                                                            <span class="text-blue-600 font-bold">Di Jalan</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-2 text-right">
                                                        <button wire:click="openEditResiModal({{ $shipment->id }})" class="text-amber-600 hover:text-amber-800 text-xs font-bold mr-2">Edit Resi</button>
                                                        <a href="{{ route('surat-jalan.download', $shipment->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold">Lihat SJ</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-sm text-zinc-500">Belum ada pengiriman.</p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">Tidak ada data pengiriman sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">{{ $orders->links() }}</div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- TAMPILAN MOBILE (CARD) -->
    <!-- ========================================== -->
    <div class="md:hidden space-y-4">
        @forelse ($orders as $order)
            @php
                $totalPesan = $order->items->sum('qty');
                $totalDalamPengiriman = $order->shipments->filter(fn($s) => $s->status->value === 'dikirim')->flatMap->items->sum('qty_dikirim');
                $totalDiterima = $order->shipments->filter(fn($s) => $s->status->value === 'diterima')->flatMap->items->sum('qty_dikirim');
                $sisaBelumKirim = $totalPesan - $totalDalamPengiriman - $totalDiterima;
                $isSelesai = ($totalDiterima >= $totalPesan && $totalPesan > 0);
            @endphp
            <div x-data="{ expanded: false }" class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-4 shadow-sm">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $order->kode_order }}</div>
                        <div class="text-xs text-zinc-500">{{ $order->created_at->format('d M Y') }}</div>
                    </div>
                    @if($isSelesai)
                        <span class="px-2.5 py-1 text-[10px] font-bold bg-green-100 text-green-800 rounded-full border border-green-200">Terkirim Semua</span>
                    @elseif($totalDalamPengiriman > 0 && $sisaBelumKirim == 0)
                        <span class="px-2.5 py-1 text-[10px] font-bold bg-blue-100 text-blue-800 rounded-full border border-blue-200">Dalam Pengiriman</span>
                    @elseif($totalDalamPengiriman > 0 || $totalDiterima > 0)
                        <span class="px-2.5 py-1 text-[10px] font-bold bg-amber-100 text-amber-800 rounded-full border border-amber-200">Dikirim Sebagian</span>
                    @else
                        <span class="px-2.5 py-1 text-[10px] font-bold bg-red-100 text-red-800 rounded-full border border-red-200">Belum Dikirim</span>
                    @endif
                </div>
                <div class="border-t border-zinc-100 dark:border-zinc-700/50 pt-2 mt-2">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-zinc-500">Customer:</span>
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $order->customer->nama_lengkap }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-3">
                        <span class="text-zinc-500">Pengiriman Ke-:</span>
                        <span class="font-bold text-zinc-900 dark:text-white">{{ $order->shipments->count() }} kali</span>
                    </div>
                    <div class="flex gap-2">
                        @if(!$isSelesai)
                            <button onclick="event.stopPropagation(); @this.openShipModal({{ $order->id }})" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg text-sm font-bold shadow-sm transition text-center">Proses Kirim</button>
                        @endif
                        <button @click="expanded = !expanded" class="flex-1 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 border border-zinc-300 px-3 py-2 rounded-lg text-sm font-bold shadow-sm transition text-center">Riwayat</button>
                    </div>
                </div>
                <!-- Accordion Mobile -->
                <div x-show="expanded" x-collapse style="display: none;" class="mt-3 pt-3 border-t border-zinc-100 dark:border-zinc-700/50">
                    @if($order->shipments->count() > 0)
                        @foreach($order->shipments as $shipment)
                            <div class="flex justify-between items-center py-1">
                                <div class="text-sm">
                                    <div class="font-bold text-zinc-900 dark:text-white">{{ $shipment->ekspedisi }}</div>
                                    <div class="text-xs text-indigo-600">{{ $shipment->no_resi ?? 'Tanpa Resi' }}</div>
                                </div>
                                <button wire:click="openEditResiModal({{ $shipment->id }})" class="text-xs text-amber-600 font-bold">Edit</button>
                            </div>
                        @endforeach
                    @else
                        <p class="text-xs text-zinc-500">Belum ada pengiriman.</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-zinc-500 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                Tidak ada data pengiriman sesuai filter.
            </div>
        @endforelse
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>

    <!-- MODAL PENGIRIMAN -->
    @if($isShipModalOpen && $selectedOrder)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Proses Pengiriman</h3>
                    <button wire:click="$set('isShipModalOpen', false)" class="text-zinc-400 hover:text-zinc-600 text-2xl leading-none">&times;</button>
                </div>
                
                <div class="p-6">
                    <div class="mb-4 bg-zinc-50 dark:bg-zinc-900/50 p-3 rounded-lg border border-zinc-100 dark:border-zinc-700">
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Order: <strong class="text-zinc-900 dark:text-white">{{ $selectedOrder->kode_order }}</strong></p>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Customer: <strong class="text-zinc-900 dark:text-white">{{ $selectedOrder->customer->nama_lengkap }}</strong></p>
                    </div>

                    <form wire:submit="processShipment" class="space-y-4">
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
                            @foreach($selectedOrder->items as $item)
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
                                    x-on:click.prevent="if (confirm('Apakah Anda yakin ingin memproses pengiriman ini? Stok akan otomatis berkurang.')) { $wire.processShipment(); }" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm flex items-center justify-center min-w-[150px]">
                                
                                <span wire:loading.remove wire:target="processShipment">Konfirmasi Pengiriman</span>
                                <span wire:loading wire:target="processShipment" class="flex items-center gap-2">
                                    @include('partials.spinner') Memproses...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL EDIT RESI -->
    @if($isEditResiModalOpen && $shipmentToEdit)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Edit Data Resi</h3>
                    <button wire:click="$set('isEditResiModalOpen', false)" class="text-zinc-400 hover:text-zinc-600 text-2xl">&times;</button>
                </div>
                <form wire:submit="updateResi" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Ekspedisi *</label>
                        <input wire:model="edit_ekspedisi" type="text" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                        @error('edit_ekspedisi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">No. Resi *</label>
                        <input wire:model="edit_no_resi" type="text" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600" placeholder="Masukkan nomor resi dari kurir">
                        @error('edit_no_resi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <button type="button" wire:click="$set('isEditResiModalOpen', false)" class="px-4 py-2 text-sm font-medium text-zinc-700 bg-zinc-100 hover:bg-zinc-200 rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm">Simpan Resi</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>