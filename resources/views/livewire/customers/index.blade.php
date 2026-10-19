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
        
        <!-- Tombol Tambah -->
        <button wire:click="create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
            + Tambah Customer
        </button>
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
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
    </div>
</div>