<div>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Pengaturan Sistem</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Konfigurasi identitas perusahaan, pajak, dan pembayaran.</p>
        </div>
        <button wire:click="save" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-bold transition shadow-sm">
            Simpan Perubahan
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- KOLOM KIRI: Info Perusahaan -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <h3 class="font-bold text-lg text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2">Identitas Perusahaan</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nama Perusahaan *</label>
                        <input wire:model="company_name" type="text" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                        @error('company_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Alamat Lengkap *</label>
                        <textarea wire:model="company_address" rows="2" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white"></textarea>
                        @error('company_address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Telepon</label>
                            <input wire:model="company_phone" type="text" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Email</label>
                            <input wire:model="company_email" type="email" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">NPWP</label>
                        <input wire:model="company_npwp" type="text" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- MANAJEMEN REKENING (MULTI-REKENING) -->
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <h3 class="font-bold text-lg text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2">Rekening Pembayaran</h3>
                
                <!-- Form Tambah Rekening -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6 bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-lg border border-zinc-100 dark:border-zinc-700">
                    <div>
                        <input wire:model="new_bank_name" type="text" placeholder="Nama Bank (BCA)" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg text-sm bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                        @error('new_bank_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <input wire:model="new_bank_account" type="text" placeholder="No. Rekening" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg text-sm bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                        @error('new_bank_account') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <input wire:model="new_bank_holder" type="text" placeholder="Atas Nama" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg text-sm bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                        @error('new_bank_holder') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <button type="button" wire:click="addBankAccount" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg text-sm font-bold transition">
                            + Tambah
                        </button>
                    </div>
                </div>

                <!-- Daftar Rekening -->
                <div class="space-y-3">
                    @forelse($bank_accounts as $bank)
                        <div class="flex items-center justify-between p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800">
                            <div class="flex items-center gap-4">
                                <!-- Toggle Checkbox Aktif -->
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:click="toggleBankAccount('{{ $bank['id'] }}')" {{ $bank['is_active'] ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-9 h-5 bg-zinc-200 peer-focus:outline-none rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-zinc-600 peer-checked:bg-green-500"></div>
                                </label>

                                <div>
                                    <div class="font-bold text-zinc-900 dark:text-white">{{ $bank['bank_name'] }} - {{ $bank['account_number'] }}</div>
                                    <div class="text-xs text-zinc-500">a/n {{ $bank['holder'] }}</div>
                                </div>
                            </div>
                            
                            <button type="button" wire:click="removeBankAccount('{{ $bank['id'] }}')" wire:confirm="Hapus rekening ini?" class="text-red-500 hover:text-red-700 text-sm font-medium">
                                Hapus
                            </button>
                        </div>
                    @empty
                        <div class="text-center text-zinc-500 text-sm py-4">Belum ada rekening yang ditambahkan.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: Pajak & Loyalty -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Setting PPN -->
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <h3 class="font-bold text-lg text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2">Pajak (PPN)</h3>
                
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Aktifkan PPN</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="ppn_enabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-zinc-200 peer-focus:outline-none rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-zinc-600 peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Persentase PPN (%)</label>
                    <input wire:model="ppn_persen" type="number" step="0.1" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                    @error('ppn_persen') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Setting Loyalty -->
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <h3 class="font-bold text-lg text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2">Loyalty Point</h3>
                
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Aktifkan Poin</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="loyalty_enabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-zinc-200 peer-focus:outline-none rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-zinc-600 peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">1 Poin per (Rp)</label>
                    <input wire:model="loyalty_poin_per_rupiah" type="number" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                    <p class="text-xs text-zinc-500 mt-1">Contoh: Isi 10.000, maka belanja Rp 10.000 dapat 1 poin.</p>
                </div>
            </div>

        </div>
    </div>
</div>