<div>
    <!-- Header Halaman -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('orders.my') }}" class="text-indigo-600 hover:underline text-sm font-medium">&larr; Kembali ke Pesanan Saya</a>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white mt-2">Detail Pesanan {{ $order->kode_order }}</h1>
        </div>
        <div>
            <!-- Badge Status Pesanan -->
            @if($order->status->value === 'menunggu_approval')
                <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm font-bold">Menunggu Approval</span>
            @elseif($order->status->value === 'dikonfirmasi')
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-bold">Dikonfirmasi</span>
            @elseif($order->status->value === 'diproses')
                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-bold">Diproses</span>
            @elseif($order->status->value === 'selesai')
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-bold">Selesai</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- KIRI: Rincian Barang & Invoice -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Rincian Barang</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-zinc-200 dark:border-zinc-700 text-sm text-zinc-500">
                                <th class="py-2">Produk</th>
                                <th class="py-2">Harga</th>
                                <th class="py-2">Qty</th>
                                <th class="py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr class="border-b border-zinc-100 dark:border-zinc-700/50">
                                    <td class="py-3 text-zinc-900 dark:text-white">{{ $item->nama_produk }}</td>
                                    <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $item->qty }}</td>
                                    <td class="py-3 text-right font-medium text-zinc-900 dark:text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-6 border-t border-zinc-200 dark:border-zinc-700 pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">Subtotal</span>
                        <span class="text-zinc-900 dark:text-white">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">PPN (Jika Ada)</span>
                        <span class="text-zinc-900 dark:text-white">Rp {{ number_format($order->ppn, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg pt-2 border-t border-zinc-100 dark:border-zinc-700">
                        <span class="text-zinc-900 dark:text-white">Total Tagihan</span>
                        <span class="text-indigo-600 dark:text-indigo-400">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Info Invoice -->
            @if($order->invoice)
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-200 dark:border-blue-800 flex justify-between items-center">
                    <p class="text-blue-800 dark:text-blue-300 font-medium">
                        Invoice: <span class="font-bold font-mono">{{ $order->invoice->nomor_invoice }}</span>
                    </p>
                    <a href="{{ route('invoice.download', $order->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm">
                        Unduh PDF
                    </a>
                </div>
            @endif
        </div>

        <!-- KANAN: Kotak Pembayaran -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm sticky top-6">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Status Pembayaran</h3>
                
                @if($order->status->value === 'menunggu_approval')
                    <p class="text-sm text-zinc-500">Pesanan Anda belum disetujui oleh Admin. Silakan tunggu konfirmasi sebelum melakukan pembayaran.</p>
                @else
                    <!-- Jika sudah ada pembayaran berstatus pending -->
                    @if($order->payments->where('status', 'pending')->count() > 0)
                        <div class="bg-amber-50 border border-amber-200 text-amber-800 p-3 rounded text-sm mb-4">
                            Bukti transfer sedang diverifikasi oleh bagian Finance.
                        </div>
                    @elseif($order->payment_status->value === 'paid')
                        <div class="bg-green-50 border border-green-200 text-green-800 p-3 rounded text-sm mb-4 font-bold text-center">
                            LUNAS
                        </div>
                    @else
                        <p class="text-sm text-zinc-500 mb-4">Silakan transfer ke:<br><strong>BCA 123-456-7890</strong><br>a/n PT Contoh Sejahtera</p>
                        
                        <!-- Form Upload -->
                        <form wire:submit="uploadBukti" class="space-y-3">
                            <div>
                                <input type="file" wire:model="bukti_transfer" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                @error('bukti_transfer') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div wire:loading wire:target="bukti_transfer" class="text-xs text-indigo-600">Mengunggah...</div>
                            
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition">
                                Unggah Bukti Transfer
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>