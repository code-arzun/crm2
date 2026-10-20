<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Manajemen Pengiriman</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Proses pengiriman dan pantau riwayat pengiriman.</p>
    </div>

    <!-- Navigasi Tab -->
    <div class="flex border-b border-zinc-200 dark:border-zinc-700 mb-6">
        <button 
            wire:click="setTab('antrean')" 
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'antrean' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}"
        >
            Antrean Pengiriman
            @if($activeTab === 'antrean' && $orders->total() > 0)
                <span class="ml-2 bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-xs">{{ $orders->total() }}</span>
            @endif
        </button>
        <button 
            wire:click="setTab('riwayat')" 
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'riwayat' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}"
        >
            Riwayat Pengiriman
        </button>
    </div>

    <!-- Tabel Data -->
    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700 text-sm font-medium text-zinc-500 dark:text-zinc-400">
                        <th class="px-6 py-4">No. Order & Customer</th>
                        <th class="px-6 py-4">Total Tagihan</th>
                        <th class="px-6 py-4">Status Order</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-mono font-medium text-zinc-900 dark:text-white">{{ $order->kode_order }}</div>
                                <div class="text-xs text-zinc-500">{{ $order->customer->nama_lengkap }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">{{ $order->status->label() }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                
                                @if($activeTab === 'antrean')
                                    <button wire:click="openShipModal({{ $order->id }})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-sm font-medium transition shadow-sm">
                                        Proses Kirim
                                    </button>
                                @else
                                    <!-- Di tab Riwayat, tampilkan tombol cetak untuk SEMUA pengiriman -->
                                    <div class="flex flex-wrap gap-2 justify-end">
                                        @foreach($order->shipments as $index => $shipment)
                                            <a href="{{ route('surat-jalan.download', $shipment->id) }}" target="_blank" class="inline-block bg-zinc-200 hover:bg-zinc-300 text-zinc-800 px-3 py-1.5 rounded text-sm font-medium transition shadow-sm">
                                                Cetak SJ #{{ $index + 1 }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">
                                @if($activeTab === 'antrean')
                                    Tidak ada antrean pengiriman saat ini.
                                @else
                                    Belum ada riwayat pengiriman.
                                @endif
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

    <!-- MODAL PENGIRIMAN (Sama seperti sebelumnya) -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Proses Pengiriman Barang</h3>
                    <button wire:click="$set('isModalOpen', false)" class="text-zinc-400 hover:text-zinc-600">&times;</button>
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
                        @foreach(\App\Models\Order::find($orderId)->items as $item)
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
                        <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 text-sm font-medium text-zinc-700 bg-zinc-100 hover:bg-zinc-200 rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm">Konfirmasi Pengiriman</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>