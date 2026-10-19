<div>
    <!-- Header Halaman -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Daftar Customer</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Kelola data pelanggan Anda di sini.</p>
        </div>
        
        <!-- Tombol Tambah (Kita buat fungsinya nanti) -->
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
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
                            <td class="px-6 py-4">
                                <div class="text-sm text-zinc-900 dark:text-white">{{ $customer->telepon }}</div>
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
                            <td class="px-6 py-4">
                                <span class="text-sm text-zinc-600 dark:text-zinc-300">
                                    {{ $customer->segment->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-indigo-600 hover:text-indigo-900 text-sm font-medium mr-3">Edit</button>
                                <button class="text-red-600 hover:text-red-900 text-sm font-medium">Hapus</button>
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
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>