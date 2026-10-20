<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Verifikasi Pembayaran</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Pantau tagihan dan bukti transfer dari customer.</p>
    </div>

    <!-- Navigasi Tab -->
    <div class="flex border-b border-zinc-200 dark:border-zinc-700 mb-6 overflow-x-auto">
        <button 
            wire:click="setTab('pending')" 
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $activeTab === 'pending' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
            Perlu Verifikasi
            @if($activeTab === 'pending' && isset($payments) && $payments->total() > 0)
                <span class="ml-2 bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-xs">{{ $payments->total() }}</span>
            @endif
        </button>
        <button 
            wire:click="setTab('unpaid')" 
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $activeTab === 'unpaid' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
            Belum Bayar
        </button>
        <button 
            wire:click="setTab('history')" 
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $activeTab === 'history' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
            Terverifikasi
        </button>
    </div>

    <!-- Konten Tabel -->
    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700 text-sm font-medium text-zinc-500 dark:text-zinc-400">
                        <th class="px-6 py-4">Order & Customer</th>
                        <th class="px-6 py-4">Jumlah Tagihan</th>
                        
                        @if($activeTab !== 'unpaid')
                            <th class="px-6 py-4">Bukti</th>
                        @endif

                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    
                    <!-- ================= TAB BELUM BAYAR ================= -->
                    @if($activeTab === 'unpaid')
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
                                    <span class="px-2.5 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Belum Bayar</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-xs text-zinc-400 italic">Menunggu transfer</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">Semua pesanan sudah dibayar atau belum disetujui.</td>
                            </tr>
                        @endforelse
                    
                    <!-- ================= TAB PENDING & HISTORY ================= -->
                    @else
                        @forelse ($payments as $payment)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-mono font-medium text-zinc-900 dark:text-white">{{ $payment->order->kode_order }}</div>
                                    <div class="text-xs text-zinc-500">{{ $payment->order->customer->nama_lengkap }}</div>
                                </td>
                                <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">
                                    Rp {{ number_format($payment->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ asset('storage/' . $payment->bukti_transfer) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium underline">
                                        Lihat Bukti
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    @if($payment->status->value === 'pending')
                                        <span class="px-2.5 py-1 text-xs font-medium bg-amber-100 text-amber-800 rounded-full">Pending</span>
                                    @elseif($payment->status->value === 'verified')
                                        <span class="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Diterima</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($payment->status->value === 'pending')
                                        <button wire:click="verify({{ $payment->id }})" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm font-medium transition shadow-sm mr-2">
                                            Verifikasi
                                        </button>
                                        <button x-data x-on:click.prevent="if(confirm('Tolak pembayaran ini?')) $wire.reject({{ $payment->id }})" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm font-medium transition shadow-sm">
                                            Tolak
                                        </button>
                                    @else
                                        <span class="text-xs text-zinc-400 italic">Selesai diproses</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">
                                    @if($activeTab === 'pending')
                                        Tidak ada pembayaran yang menunggu verifikasi.
                                    @else
                                        Belum ada riwayat verifikasi pembayaran.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Dinamis -->
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            @if($activeTab === 'unpaid')
                {{ $orders->links() }}
            @else
                {{ $payments->links() }}
            @endif
        </div>
    </div>
</div>