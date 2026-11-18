<div>
    <!-- Notifikasi Sukses -->
    {{-- @if (session()->has('message'))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-200" role="alert">
            {{ session('message') }}
        </div>
    @endif --}}
    

    <!-- Header Halaman -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Daftar Customer</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Kelola data pelanggan Anda di sini.</p>
        </div>
        
        <div class="flex gap-2">
            <button wire:click="$set('isImportModalOpen', true)" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Import Excel
            </button>
            <button wire:click="create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm">
                + Tambah Customer
            </button>
        </div>
    </div>

    <!-- Kotak Pencarian -->
    <div class="mb-4">
        <input 
            wire:model.live.debounce.300ms="search"
            type="text" 
            placeholder="Cari nama, telepon, atau perusahaan..." 
            class="w-full md:w-1/3 px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
        >
    </div>

    <!-- Tabel Data -->
    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700 text-sm font-medium text-zinc-500 dark:text-zinc-400">
                        <th class="px-6 py-4">Nama & Perusahaan</th>
                        <th class="px-6 py-4">Kontak</th>
                        <th class="px-6 py-4">Tier</th>
                        <th class="px-6 py-4">Segmen</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($customers as $customer)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $customer->nama_lengkap }}</div>
                                <div class="text-sm text-zinc-500">{{ $customer->nama_perusahaan ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-white">
                                {{ $customer->telepon }}
                            </td>
                            <td class="px-6 py-4">
                                @if($customer->tier)
                                    <span class="px-2.5 py-1 text-xs font-medium bg-amber-100 text-amber-800 rounded-full">
                                        {{ $customer->tier->nama }}
                                    </span>
                                @else
                                    <span class="text-zinc-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $customer->segment->label() }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="edit({{ $customer->id }})" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium mr-3">Edit</button>
                                <button wire:click="delete({{ $customer->id }})" wire:confirm="Yakin ingin menghapus customer ini?" class="text-red-600 hover:text-red-900 text-sm font-medium">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">
                                Tidak ada data customer yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($customers->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL FORM (POP-UP) -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
                
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">
                        {{ $customerId ? 'Edit Customer' : 'Tambah Customer Baru' }}
                    </h3>
                    <button wire:click="closeModal" class="text-zinc-400 hover:text-zinc-600">&times;</button>
                </div>

                <form wire:submit="store" class="p-6 space-y-4">
                    
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nama Lengkap *</label>
                        <input wire:model="nama_lengkap" type="text" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                        @error('nama_lengkap') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nomor Telepon *</label>
                        <input wire:model="telepon" type="text" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                        @error('telepon') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nama Perusahaan</label>
                        <input wire:model="nama_perusahaan" type="text" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                        @error('nama_perusahaan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tier Pelanggan</label>
                        <select wire:model="tier_id" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Pilih Tier (Opsional) --</option>
                            @foreach($tiers as $tier)
                                <option value="{{ $tier->id }}">{{ $tier->nama }}</option>
                            @endforeach
                        </select>
                        @error('tier_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Alamat Lengkap</label>
                        <textarea wire:model="alamat" rows="3" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        @error('alamat') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-zinc-700 bg-zinc-100 hover:bg-zinc-200 rounded-lg transition">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition shadow-sm">Simpan Data</button>
                    </div>

                </form>
            </div>
        </div>
    @endif

    <!-- Toast Notifikasi Modern -->
    <div 
            x-data="{ show: false, message: '' }" 
            x-on:notify.window="
                message = $event.detail.message; 
                show = true; 
                setTimeout(() => show = false, 3000)
            "
            x-show="show" 
            x-transition.opacity.duration.300ms
            class="fixed bottom-6 right-6 z-50 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2" 
            style="display: none;"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span x-text="message" class="font-medium"></span>
    </div>
    {{-- </div> --}}

    <!-- MODAL IMPORT EXCEL -->
    @if($isImportModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Import Customer dari Excel</h3>
                    <button wire:click="$set('isImportModalOpen', false)" class="text-zinc-400 hover:text-zinc-600 text-2xl">&times;</button>
                </div>
                
                <form wire:submit="import" class="p-6 space-y-4">
                    <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                        <p class="text-sm text-blue-800 font-medium mb-2">Langkah-langkah:</p>
                        <ol class="text-xs text-blue-700 list-decimal list-inside space-y-1 mb-3">
                            <li>Unduh template Excel di bawah ini.</li>
                            <li>Isi data customer sesuai kolom.</li>
                            <li>Simpan file, lalu unggah kembali di sini.</li>
                        </ol>
                        <button type="button" wire:click="downloadTemplate" class="w-full bg-white border border-blue-300 text-blue-700 px-3 py-2 rounded text-sm font-bold hover:bg-blue-50 transition flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download Template Excel
                        </button>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Upload File Excel</label>
                        <input type="file" wire:model="file_excel" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" accept=".xlsx, .xls, .csv">
                        @error('file_excel') <span class="text-red-500 text-xs block mt-2">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <button type="button" wire:click="$set('isImportModalOpen', false)" class="px-4 py-2 text-sm font-medium text-zinc-700 bg-zinc-100 hover:bg-zinc-200 rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm flex items-center justify-center">
                            <span wire:loading.remove wire:target="import">Mulai Import</span>
                            <span wire:loading wire:target="import" class="flex items-center gap-2">
                                @include('partials.spinner') Memproses...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>