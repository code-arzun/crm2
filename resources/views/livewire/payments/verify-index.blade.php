<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Verifikasi Pembayaran (Finance)</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Pantau tagihan dan verifikasi bukti transfer.</p>
        </div>
        <!-- Tombol Export Excel -->
        <div x-data="{ openExport: false }" class="relative">
            <button @click="openExport = !openExport" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Excel
            </button>

            <div x-show="openExport" @click.away="openExport = false" style="display: none;" class="absolute right-0 mt-2 w-72 bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-zinc-200 dark:border-zinc-700 p-4 z-50">
                <h4 class="font-bold text-sm mb-3">Filter Tanggal Export</h4>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs text-zinc-500 mb-1">Dari Tanggal</label>
                        <input type="date" wire:model="exportStartDate" class="w-full px-3 py-1.5 text-sm border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-500 mb-1">Sampai Tanggal</label>
                        <input type="date" wire:model="exportEndDate" class="w-full px-3 py-1.5 text-sm border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                    </div>
                    <button wire:click="export" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg text-sm font-bold transition mt-2">
                        Unduh Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TOOLBAR FILTER -->
    <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-xs font-medium text-zinc-500 mb-1">Pencarian</label>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari No. Order atau Nama Customer..." class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <!-- FILTER STATUS PEMBAYARAN (BARU) -->
            <div class="w-full md:w-56">
                <label class="block text-xs font-medium text-zinc-500 mb-1">Status Pembayaran</label>
                <select wire:model.live="filterPayStatus" class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="semua">Semua Status</option>
                    <option value="pending">Butuh Verifikasi</option>
                    <option value="unpaid">Belum Bayar</option>
                    <option value="partial">DP / Sebagian</option>
                    <option value="paid">Lunas</option>
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
                        <th class="px-6 py-4">Total Tagihan</th>
                        <th class="px-6 py-4">Status Pembayaran</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($orders as $order)
                        <!-- ROW BISA DIKLIK KE HALAMAN DETAIL ADMIN -->
                        <tr onclick="window.location='{{ route('admin.orders.show', $order->id) }}'" class="hover:bg-indigo-50/50 dark:hover:bg-zinc-700/50 transition cursor-pointer">
                            
                            <td class="px-6 py-4">
                                <div class="font-mono font-medium text-indigo-600 dark:text-indigo-400">{{ $order->kode_order }}</div>
                                <div class="text-xs text-zinc-500">{{ $order->created_at->format('d M Y H:i') }}</div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $order->customer->nama_lengkap }}</div>
                                <div class="text-xs text-zinc-500">{{ $order->customer->nama_perusahaan ?? '-' }}</div>
                            </td>
                            
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                            
                            <td class="px-6 py-4">
                                @php
                                    $hasPending = $order->payments->where('status', 'pending')->count() > 0;
                                @endphp

                                @if($hasPending)
                                    <span class="px-2.5 py-1 text-xs font-bold bg-amber-100 text-amber-800 rounded-full border border-amber-200">Butuh Verifikasi</span>
                                @elseif($order->payment_status->value === 'paid')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full border border-green-200">Lunas</span>
                                @elseif($order->payment_status->value === 'partial')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-indigo-100 text-indigo-800 rounded-full border border-indigo-200">DP / Sebagian</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold bg-red-100 text-red-800 rounded-full border border-red-200">Belum Bayar</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-right">
                                <!-- TOMBOL VERIFIKASI -->
                                @if($hasPending)
                                    <button onclick="event.stopPropagation(); @this.openPayModal({{ $order->id }})" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm font-bold shadow-sm transition">
                                        Verifikasi
                                    </button>
                                @else
                                    <span class="text-xs text-zinc-400 italic">Lihat Detail &rarr;</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">
                                Tidak ada data pesanan sesuai filter.
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

    <!-- MODAL VERIFIKASI PEMBAYARAN -->
    @if($isPayModalOpen && $selectedOrder)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4">
            <!-- Lebar modal diperbesar menjadi max-w-2xl agar gambar muat -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Verifikasi Pembayaran</h3>
                    <button wire:click="$set('isPayModalOpen', false)" class="text-zinc-400 hover:text-zinc-600 text-2xl leading-none">&times;</button>
                </div>
                <div class="p-6">
                    <div class="mb-5 bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 flex flex-wrap gap-6">
                <div>
                    <div class="text-[10px] text-zinc-500 uppercase tracking-wider font-bold mb-1">No. Order</div>
                    <div class="text-sm font-bold text-indigo-600 font-mono">{{ $selectedOrder->kode_order }}</div>
                </div>
                <div>
                    <div class="text-[10px] text-zinc-500 uppercase tracking-wider font-bold mb-1">Nama Customer</div>
                    <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $selectedOrder->customer->nama_lengkap }}</div>
                </div>
                <div>
                    <div class="text-[10px] text-zinc-500 uppercase tracking-wider font-bold mb-1">Perusahaan</div>
                    <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $selectedOrder->customer->nama_perusahaan ?? '-' }}</div>
                </div>
            </div>

                    @foreach($selectedOrder->payments->where('status', 'pending') as $payment)
                        <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 mb-4 shadow-sm">
                            
                            <!-- Rincian Nominal -->
                            <div class="flex justify-between items-center mb-3">
                                <div>
                                    <span class="text-xs text-zinc-500 block">Nominal Transfer</span>
                                    <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($payment->jumlah, 0, ',', '.') }}</span>
                                </div>
                                <div>
                                    <span class="text-xs text-zinc-500 block">Tanggal Bayar</span>
                                    <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ $payment->created_at->format('H:i') }}, {{ $payment->created_at->format('d M Y') }}</span>
                                </div>
                            </div>

                            <!-- PREVIEW GAMBAR BUKTI TRANSFER -->
                            <div class="mb-4">
                                <span class="text-xs text-zinc-500 block mb-2">Bukti Transfer:</span>
                                @if(Str::endsWith($payment->bukti_transfer, ['.jpg', '.jpeg', '.png']))
                                    <!-- Jika gambar, tampilkan preview langsung -->
                                    <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden bg-zinc-50 dark:bg-zinc-900 flex justify-center p-2">
                                        <a href="{{ asset('storage/'.$payment->bukti_transfer) }}" target="_blank" title="Klik untuk memperbesar">
                                            <img src="{{ asset('storage/'.$payment->bukti_transfer) }}" class="max-h-64 object-contain rounded hover:opacity-80 transition" alt="Bukti Transfer">
                                        </a>
                                    </div>
                                @else
                                    <!-- Jika PDF, tampilkan tombol link -->
                                    <a href="{{ asset('storage/'.$payment->bukti_transfer) }}" target="_blank" class="flex items-center justify-center gap-2 w-full bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-4 py-3 rounded-lg border border-indigo-200 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        Buka Dokumen PDF
                                    </a>
                                @endif
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="flex gap-3 mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-700">
                                <button wire:click="verify({{ $payment->id }})" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg text-sm font-bold transition shadow-sm flex justify-center items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Verifikasi
                                </button>
                                <button wire:click="reject({{ $payment->id }})" class="flex-1 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-4 py-2.5 rounded-lg text-sm font-bold transition shadow-sm flex justify-center items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Tolak
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>