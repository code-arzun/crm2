<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Profil Saya</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Kelola informasi akun dan keamanan Anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- KOLOM KIRI: Form Profil -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2">Informasi Akun</h3>
                
                <form wire:submit="updateProfile" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Nama Lengkap *</label>
                            <input wire:model="name" type="text" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Email *</label>
                            <input wire:model="email" type="email" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Nomor Telepon / WA</label>
                        <input wire:model="telepon" type="text" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                        @error('telepon') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- DATA CUSTOMER (Hanya muncul jika role customer) -->
                    @if(auth()->user()->hasRole('customer'))
                        <div class="pt-4 mt-4 border-t border-zinc-100 dark:border-zinc-700">
                            <h4 class="font-bold text-sm text-zinc-900 dark:text-white mb-3">Informasi Perusahaan</h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Nama Perusahaan</label>
                                    <input wire:model="nama_perusahaan" type="text" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                                    @error('nama_perusahaan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Kategori Bisnis</label>
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
                                    <label class="block text-sm font-medium mb-1">Alamat Lengkap</label>
                                    <textarea wire:model="alamat" rows="2" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600"></textarea>
                                    @error('alamat') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-bold transition shadow-sm flex items-center justify-center">
                            <span wire:loading.remove wire:target="updateProfile">Simpan Profil</span>
                            <span wire:loading wire:target="updateProfile" class="flex items-center gap-2">
                                @include('partials.spinner') Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- KOLOM KANAN: Ubah Password -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm sticky top-6">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2">Ubah Password</h3>
                
                <form wire:submit="updatePassword" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Password Saat Ini *</label>
                        <input wire:model="current_password" type="password" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                        @error('current_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Password Baru *</label>
                        <input wire:model="new_password" type="password" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                        @error('new_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Konfirmasi Password Baru *</label>
                        <input wire:model="new_password_confirmation" type="password" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                    </div>

                    <button type="submit" class="w-full bg-zinc-800 hover:bg-zinc-900 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm flex justify-center items-center">
                        <span wire:loading.remove wire:target="updatePassword">Ubah Password</span>
                        <span wire:loading wire:target="updatePassword" class="flex items-center gap-2">
                            @include('partials.spinner') Memproses...
                        </span>
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>