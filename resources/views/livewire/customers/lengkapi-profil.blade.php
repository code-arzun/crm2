<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white dark:bg-zinc-800 p-8 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white mb-2">Lengkapi Profil Perusahaan</h1>
        <p class="text-sm text-zinc-500 mb-6">Sebelum mulai berbelanja, mohon lengkapi data pengiriman Anda.</p>

        <form wire:submit="simpan" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Nama Perusahaan / Usaha *</label>
                <input wire:model="nama_perusahaan" type="text" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                @error('nama_perusahaan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Kategori Bisnis *</label>
                <select wire:model="kategori_bisnis" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="UMKM">UMKM</option>
                    <option value="Supplier Makanan">Supplier Makanan</option>
                    <option value="Retail">Retail / Toko</option>
                    <option value="Jasa">Perusahaan Jasa</option>
                </select>
                @error('kategori_bisnis') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Alamat Lengkap Pengiriman *</label>
                <textarea wire:model="alamat" rows="3" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600"></textarea>
                @error('alamat') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-lg font-bold transition mt-4">
                Simpan & Lanjut Belanja
            </button>
        </form>
    </div>
</div>