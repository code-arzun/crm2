<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Penerimaan Barang Retur</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Konfirmasi dan pantau status barang retur dari customer.</p>
        </div>
    </div>

    <!-- TOOLBAR FILTER -->
    <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-xs font-medium text-zinc-500 mb-1">Pencarian</label>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari No. Order atau Nama Customer..." class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
            </div>
            <div class="w-full md:w-56">
                <label class="block text-xs font-medium text-zinc-500 mb-1">Status Retur</label>
                <select wire:model.live="filterStatus" class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                    <option value="semua">Semua Status</option>
                    <option value="disetujui">Menunggu Diterima</option>
                    <option value="selesai">Diterima</option>
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
                        <th class="px-6 py-4">No. Order & Customer</th>
                        <th class="px-6 py-4">Dikonfirmasi oleh</th>
                        <th class="px-6 py-4 text-center">Qty</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($returns as $return)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-mono font-medium text-indigo-600 dark:text-indigo-400">{{ $return->order->kode_order }}</div>
                                <div class="text-xs text-zinc-500">{{ $return->order->customer->nama_lengkap }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($return->approver)
                                    <div class="font-medium text-zinc-900 dark:text-white">{{ $return->approver->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ $return->updated_at->format('d M Y H:i')}}</div>
                                @else
                                    <span class="text-xs text-zinc-400 italic">Belum dikonfirmasi</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-zinc-900 dark:text-white">{{ $return->total_qty_diretur }}</td>
                            <td class="px-6 py-4">
                                @if($return->status->value === 'disetujui')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-amber-100 text-amber-800 rounded-full border border-amber-200">Menunggu Diterima</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full border border-green-200">Diterima</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <!-- Tombol Cek Detail -->
                                <button wire:click="openDetailModal({{ $return->id }})" class="text-indigo-600 hover:text-indigo-900 text-sm font-bold mr-3">
                                    Detail
                                </button>

                                <!-- Tombol Terima Barang -->
                                @if($return->status->value === 'disetujui')
                                    <button wire:click="terimaBarang({{ $return->id }})" wire:confirm="Konfirmasi bahwa barang sudah diterima di gudang?" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm font-bold shadow-sm transition">
                                        Barang Diterima
                                    </button>
                                @else
                                    <span class="text-xs text-zinc-400 italic">Stok telah ditambahkan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">
                                Tidak ada data retur sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($returns->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">{{ $returns->links() }}</div>
        @endif
    </div>

    <!-- MODAL DETAIL RETUR -->
    @if($isDetailModalOpen && $selectedReturnDetail)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Detail Barang Retur</h3>
                    <button wire:click="$set('isDetailModalOpen', false)" class="text-zinc-400 hover:text-zinc-600 text-2xl">&times;</button>
                </div>
                
                <div class="p-6">
                    <!-- Info Ringkas -->
                    <div class="bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-lg border border-zinc-200 dark:border-zinc-700 mb-4 flex justify-between items-center">
                        <div>
                            <div class="text-xs text-zinc-500">Nomor Retur</div>
                            <div class="font-mono font-bold text-indigo-600 text-sm">{{ $selectedReturnDetail->kode_retur }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-zinc-500">Customer</div>
                            <div class="font-medium text-zinc-900 dark:text-white">{{ $selectedReturnDetail->order->customer->nama_lengkap }}</div>
                        </div>
                    </div>

                    <!-- Daftar Barang yang Diretur -->
                    <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden mb-4">
                        <table class="w-full text-left">
                            <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700">
                                <tr>
                                    <th class="px-4 py-2 text-xs font-medium text-zinc-500">Barang</th>
                                    <th class="px-4 py-2 text-xs font-medium text-zinc-500 text-center">Qty</th>
                                    <th class="px-4 py-2 text-xs font-medium text-zinc-500">Alasan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                                @foreach($selectedReturnDetail->items as $retItem)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-zinc-900 dark:text-white">
                                            {{ $retItem->orderItem->product->nama ?? 'Produk Dihapus' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center font-bold text-zinc-900 dark:text-white">
                                            {{ $retItem->qty }}
                                        </td>
                                        <td class="px-4 py-3 text-xs text-zinc-600 dark:text-zinc-400 capitalize">
                                            {{ str_replace('_', ' ', $retItem->alasan_retur) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Catatan Customer -->
                    @if($selectedReturnDetail->alasan)
                        <div>
                            <div class="text-xs text-zinc-500 mb-1">Catatan Keluhan Customer:</div>
                            <div class="text-sm text-zinc-700 dark:text-zinc-300 bg-amber-50 dark:bg-amber-900/20 p-3 rounded-lg border border-amber-200 dark:border-amber-800 italic">
                                "{{ $selectedReturnDetail->alasan }}"
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50 flex justify-end">
                    <button wire:click="$set('isDetailModalOpen', false)" class="px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 hover:bg-zinc-50 rounded-lg shadow-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
    
</div>