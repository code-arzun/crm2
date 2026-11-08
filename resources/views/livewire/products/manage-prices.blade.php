<div>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('products.index') }}" class="text-indigo-600 hover:underline text-sm font-medium">&larr; Kembali ke Produk</a>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white mt-2">Harga Khusus: {{ $product->nama }}</h1>
            <p class="text-sm text-zinc-500 mt-1">Harga Default: <strong>Rp {{ number_format($product->harga_default, 0, ',', '.') }}</strong></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- FORM TAMBAH HARGA -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm sticky top-6">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Tambah Harga Baru</h3>
                
                <form wire:submit="simpanHarga" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Pilih Spesifik Customer (Opsional)</label>
                        <select wire:model="customer_id" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                            <option value="">-- Pilih Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->nama_lengkap }} ({{ $c->nama_perusahaan }})</option>
                            @endforeach
                        </select>
                        @error('customer_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="text-center text-sm text-zinc-500 font-bold">ATAU</div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Pilih Berdasarkan Tier (Opsional)</label>
                        <select wire:model="tier_id" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                            <option value="">-- Pilih Tier --</option>
                            @foreach($tiers as $t)
                                <option value="{{ $t->id }}">{{ $t->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Harga Khusus (Rp) *</label>
                        <input wire:model="harga" type="number" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                        @error('harga') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium mb-1">Berlaku Dari (Opsional)</label>
                            <input wire:model="berlaku_dari" type="date" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1">Sampai (Opsional)</label>
                            <input wire:model="berlaku_sampai" type="date" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600 text-sm">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-bold transition mt-2">
                        Simpan Harga
                    </button>
                </form>
            </div>
        </div>

        <!-- TABEL HARGA -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700 text-sm font-medium text-zinc-500">
                            <th class="px-6 py-4">Target</th>
                            <th class="px-6 py-4">Harga Khusus</th>
                            <th class="px-6 py-4">Masa Berlaku</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($prices as $price)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-zinc-900 dark:text-white">
                                        {{ $price->targetLabel() }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-bold text-indigo-600">
                                    Rp {{ number_format($price->harga, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-500">
                                    @if($price->berlaku_dari || $price->berlaku_sampai)
                                        {{ $price->berlaku_dari ? $price->berlaku_dari->format('d/m/y') : 'Kapan saja' }} 
                                        s/d 
                                        {{ $price->berlaku_sampai ? $price->berlaku_sampai->format('d/m/y') : 'Selamanya' }}
                                    @else
                                        Selamanya
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="hapusHarga({{ $price->id }})" wire:confirm="Hapus harga khusus ini?" class="text-red-500 hover:text-red-700 text-sm font-medium">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-zinc-500">Belum ada harga khusus untuk produk ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($prices->hasPages())
                    <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">{{ $prices->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>