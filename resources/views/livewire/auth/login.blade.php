<div>
    <div class="text-center mb-6">
        <h3 class="text-xl font-bold text-zinc-900 dark:text-white">Masuk ke Akun</h3>
        <p class="text-sm text-zinc-500 mt-1">Gunakan email dan password Anda</p>
    </div>

    <form wire:submit="login" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Email</label>
            <input wire:model="email" type="email" required autofocus class="w-full px-4 py-2.5 border border-zinc-300 dark:border-zinc-600 rounded-xl bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            @error('email') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Password</label>
            <input wire:model="password" type="password" required class="w-full px-4 py-2.5 border border-zinc-300 dark:border-zinc-600 rounded-xl bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            @error('password') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input wire:model="remember" type="checkbox" class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm text-zinc-600 dark:text-zinc-400">Ingat saya</span>
            </label>
        </div>

        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-bold transition shadow-sm flex justify-center items-center">
            <span wire:loading.remove wire:target="login">Masuk</span>
            <span wire:loading wire:target="login" class="flex items-center gap-2">
                @include('partials.spinner') Memproses...
            </span>
        </button>
    </form>

    <div class="mt-6 text-center text-sm text-zinc-600 dark:text-zinc-400">
        Belum punya akun? 
        <button type="button" onclick="window.dispatchEvent(new CustomEvent('switch-to-register'))" class="font-bold text-indigo-600 hover:text-indigo-500 underline">Daftar di sini</button>
    </div>
</div>