<div>
    <div class="text-center mb-6">
        <h3 class="text-xl font-bold text-zinc-900 dark:text-white">Daftar Akun Baru</h3>
        <p class="text-sm text-zinc-500 mt-1">Hanya butuh beberapa detik</p>
    </div>

    <form wire:submit="register" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nama Lengkap</label>
            <input wire:model="name" type="text" required autofocus class="w-full px-4 py-2.5 border border-zinc-300 dark:border-zinc-600 rounded-xl bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            @error('name') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nomor WhatsApp</label>
            <input wire:model="telepon" type="text" required class="w-full px-4 py-2.5 border border-zinc-300 dark:border-zinc-600 rounded-xl bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            @error('telepon') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Email</label>
            <input wire:model="email" type="email" required class="w-full px-4 py-2.5 border border-zinc-300 dark:border-zinc-600 rounded-xl bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            @error('email') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Password</label>
            <input wire:model="password" type="password" required class="w-full px-4 py-2.5 border border-zinc-300 dark:border-zinc-600 rounded-xl bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            @error('password') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Konfirmasi Password</label>
            <input wire:model="password_confirmation" type="password" required class="w-full px-4 py-2.5 border border-zinc-300 dark:border-zinc-600 rounded-xl bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
        </div>

        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-bold transition shadow-sm flex justify-center items-center">
            <span wire:loading.remove wire:target="register">Daftar Sekarang</span>
            <span wire:loading wire:target="register" class="flex items-center gap-2">
                @include('partials/spinner') Memproses...
            </span>
        </button>
    </form>

    <div class="mt-6 text-center text-sm text-zinc-600 dark:text-zinc-400">
        Sudah punya akun? 
        <button type="button" onclick="window.dispatchEvent(new CustomEvent('switch-to-login'))" class="font-bold text-indigo-600 hover:text-indigo-500 underline">Masuk di sini</button>
    </div>
</div>