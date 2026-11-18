<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Manajemen Retur</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Kelola pengajuan pengembalian barang dari customer.</p>
    </div>

    <!-- Toolbar -->
    <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari No. Order atau Customer..." class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
            </div>
            <div class="w-full md:w-48">
                <select wire:model.live="filterStatus" class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                    <option value="semua">Semua Status</option>
                    <option value="diajukan">Menunggu Persetujuan</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabel -->
    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700 text-sm font-medium text-zinc-500">
                        <th class="px-6 py-4">No. Order & Customer</th>
                        <th class="px-6 py-4 text-center">Qty</th>
                        <th class="px-6 py-4">Alasan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($returns as $return)
                        <tr onclick="window.location='{{ route('returns.show', $return->id) }}'" class="hover:bg-indigo-50/50 dark:hover:bg-zinc-700/50 transition cursor-pointer">
                            <td class="px-6 py-4">
                                <div class="font-mono font-medium text-indigo-600">{{ $return->kode_retur }}</div>
                                <div class="text-xs text-zinc-500">{{ $return->order->customer->nama_lengkap }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ $return->items->first()->orderItem->product->nama ?? '-' }} 
                                @if($return->items->count() > 1)
                                    <span class="text-xs text-zinc-500">(+{{ $return->items->count() - 1 }} lainnya)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center font-bold">{{ $return->total_qty_diretur }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="capitalize">{{ str_replace('_', ' ', $return->items->first()->alasan_retur ?? '-') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($return->status->value === 'diajukan')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-amber-100 text-amber-800 rounded-full border border-amber-200">Menunggu</span>
                                @elseif($return->status->value === 'selesai')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full border border-green-200">Selesai</span>
                                @elseif($return->status->value === 'disetujui')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-blue-100 text-blue-800 rounded-full border border-blue-200">Diproses</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold bg-red-100 text-red-800 rounded-full border border-red-200">Ditolak</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($return->status->value === 'diajukan')
                                    <!-- TOMBOL PROSES (MEMBUKA MODAL) -->
                                    <button onclick="event.stopPropagation(); @this.openModal({{ $return->id }})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-sm font-bold shadow-sm transition flex justify-center items-center min-w-[80px]">
                                        Proses
                                    </button>
                                @else
                                    <span class="text-xs text-zinc-400 italic">Lihat Detail &rarr;</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-zinc-500">Tidak ada pengajuan retur.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($returns->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">{{ $returns->links() }}</div>
        @endif
    </div>

    <!-- MODAL PROSES RETUR -->
    @if($isModalOpen && $selectedReturn)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Proses Retur</h3>
                    <button wire:click="$set('isModalOpen', false)" class="text-zinc-400 hover:text-zinc-600 text-2xl">&times;</button>
                </div>
                
                <form class="p-6 space-y-4">
                    <div class="bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-lg border border-zinc-200 dark:border-zinc-700 mb-4">
                        <p class="text-sm font-bold text-zinc-900 dark:text-white mb-3">Daftar Barang Retur:</p>
                        <ul class="space-y-2">
                            @foreach($selectedReturn->items as $item)
                                <li class="flex justify-between items-center text-sm border-b border-zinc-200 dark:border-zinc-700/50 pb-2 last:border-0 last:pb-0">
                                    <div>
                                        <div class="font-medium text-zinc-900 dark:text-white">{{ $item->orderItem->product->nama ?? 'Produk Dihapus' }}</div>
                                        <div class="text-xs text-zinc-500">Alasan: {{ str_replace('_', ' ', $item->alasan_retur) }}</div>
                                    </div>
                                    <div class="font-bold text-zinc-900 dark:text-white">{{ $item->qty }} pcs</div>
                                </li>
                            @endforeach
                        </ul>
                        @if($selectedReturn->alasan)
                            <div class="mt-3 pt-3 border-t border-zinc-200 dark:border-zinc-700">
                                <div class="text-xs text-zinc-500 mb-1">Catatan dari Customer:</div>
                                <div class="text-sm text-zinc-700 dark:text-zinc-300 italic">"{{ $selectedReturn->alasan }}"</div>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-between items-center pt-4 border-t border-zinc-200 dark:border-zinc-700 mt-6">
                        <button type="button" wire:click="tolakRetur" wire:confirm="Tolak pengajuan retur ini?" class="px-4 py-2 text-sm font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                            Tolak Retur
                        </button>
                        <button type="button" wire:click="setujuiRetur" wire:confirm="Setujui retur ini dan proses refund?" class="px-4 py-2 text-sm font-bold text-white bg-green-600 hover:bg-green-700 rounded-lg shadow-sm transition">
                            Setujui & Refund
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>