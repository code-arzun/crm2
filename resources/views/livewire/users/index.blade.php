<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Manajemen Staf</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Kelola akun karyawan internal (Admin, Sales, Finance, Gudang).</p>
        </div>
        <button wire:click="create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm">
            + Tambah Staf
        </button>
    </div>

    <!-- Toolbar -->
    <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-xs font-medium text-zinc-500 mb-1">Pencarian</label>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau email..." class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
            </div>
            <div class="w-full md:w-48">
                <label class="block text-xs font-medium text-zinc-500 mb-1">Status</label>
                <select wire:model.live="filterStatus" class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                    <option value="semua">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Resign / Nonaktif</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabel -->
    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700 text-sm font-medium text-zinc-500 dark:text-zinc-400">
                        <th class="px-6 py-4">Nama & Email</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Tgl Masuk</th>
                        <th class="px-6 py-4">Tgl Resign</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($users as $user)
                        <tr class="hover:bg-indigo-50/50 dark:hover:bg-zinc-700/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $user->name }}</div>
                                <div class="text-xs text-zinc-500">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-bold bg-zinc-100 text-zinc-800 rounded-full border border-zinc-200 uppercase">
                                    {{ $user->roles->first()?->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ $user->join_date ? $user->join_date->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-red-500">
                                {{ $user->resign_date ? $user->resign_date->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($user->is_active)
                                    <span class="px-2.5 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full">Aktif</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold bg-red-100 text-red-800 rounded-full">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="edit({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium mr-3">Edit</button>
                                
                                @if($user->id !== auth()->id())
                                    <button wire:click="toggleActive({{ $user->id }})" wire:confirm="Ubah status user ini?" class="{{ $user->is_active ? 'text-red-500 hover:text-red-700' : 'text-green-600 hover:text-green-800' }} text-sm font-medium">
                                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-zinc-500">Tidak ada data staf.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">{{ $users->links() }}</div>
        @endif
    </div>

    <!-- MODAL USER -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">{{ $userId ? 'Edit Staf' : 'Tambah Staf Baru' }}</h3>
                    <button wire:click="closeModal" class="text-zinc-400 hover:text-zinc-600 text-2xl">&times;</button>
                </div>
                <form wire:submit="store" class="p-6 space-y-4">
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
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Role / Jabatan *</label>
                            <select wire:model="role" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                                <option value="">-- Pilih Role --</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->name }}">{{ ucfirst($r->name) }}</option>
                                @endforeach
                            </select>
                            @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Tanggal Masuk *</label>
                            <input wire:model="join_date" type="date" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                            @error('join_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Password {{ $userId ? '(Kosongkan jika tidak diubah)' : '*' }}</label>
                        <input wire:model="password" type="password" class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-900 dark:border-zinc-600">
                        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-zinc-700 bg-zinc-100 hover:bg-zinc-200 rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>