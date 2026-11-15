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

    <!-- TABEL -->
    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700 text-sm font-medium text-zinc-500 dark:text-zinc-400">
                        <th class="px-6 py-4">No. Order & Tanggal</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Pengiriman ke-</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($orders as $order)
                        @php
                            $totalPesan = $order->items->sum('qty');
                            
                            $totalDalamPengiriman = $order->shipments
                                ->filter(fn($s) => $s->status->value === 'dikirim')
                                ->flatMap->items
                                ->sum('qty_dikirim');

                            $totalDiterima = $order->shipments
                                ->filter(fn($s) => $s->status->value === 'diterima')
                                ->flatMap->items
                                ->sum('qty_dikirim');

                            $sisaBelumKirim = $totalPesan - $totalDalamPengiriman - $totalDiterima;
                            
                            // KUNCI PERBAIKAN: Cek status 'diterima'
                            $isSelesai = ($totalDiterima >= $totalPesan && $totalPesan > 0);
                        @endphp

                        <tr onclick="window.location='{{ route('admin.orders.show', $order->id) }}'" class="hover:bg-indigo-50/50 dark:hover:bg-zinc-700/50 transition cursor-pointer">
                            
                            <td class="px-6 py-4">
                                <div class="font-mono font-medium text-indigo-600 dark:text-indigo-400">{{ $order->kode_order }}</div>
                                <div class="text-xs text-zinc-500">{{ $order->created_at->format('d M Y H:i') }}</div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $order->customer->nama_lengkap }}</div>
                                <div class="text-xs text-zinc-500">{{ $order->customer->alamat ?? '-' }}</div>
                            </td>
                            
                            <!-- STATUS PENGIRIMAN (3 TAHAP) -->
                            <td class="px-6 py-4">
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
                                @else
                                    <span class="text-zinc-400">-</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-right">
                                <!-- TOMBOL BARU: Edit Resi (Hanya muncul jika sudah pernah dikirim) -->
                                @if($order->shipments->count() > 0)
                                    @php $lastShipment = $order->shipments->sortByDesc('id')->first(); @endphp
                                    <button onclick="event.stopPropagation(); @this.openEditResiModal({{ $lastShipment->id }})" class="bg-amber-100 hover:bg-amber-200 text-amber-800 border border-amber-300 px-3 py-1.5 rounded text-sm font-bold shadow-sm transition mr-2">
                                        Edit Resi
                                    </button>
                                @endif

                                <!-- Tombol Proses Kirim (Jika belum selesai) -->
                                @if(!$isSelesai)
                                    <button onclick="event.stopPropagation(); @this.openShipModal({{ $order->id }})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-sm font-bold shadow-sm transition">
                                        Proses Kirim
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">
                                Tidak ada data pengiriman sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">{{ $orders->links() }}</div>
        @endif
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