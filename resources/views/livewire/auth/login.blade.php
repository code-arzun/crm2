<div>
    <flux:heading size="xl" level="1">Masuk ke Akun Anda</flux:heading>
    <flux:subheading size="lg" class="mb-6">Gunakan email dan password Anda</flux:subheading>

    <form wire:submit="login" class="space-y-6">
        <flux:input wire:model="email" label="Email" type="email" required autofocus autocomplete="email" />
        <flux:input wire:model="password" label="Password" type="password" required autocomplete="current-password" />

        <div class="flex items-center justify-between">
            <flux:checkbox wire:model="remember" label="Ingat saya" />
        </div>

        <flux:button variant="primary" type="submit" class="w-full">Masuk</flux:button>
    </form>

    <div class="mt-6 text-center text-sm text-zinc-600 dark:text-zinc-400">
        Belum punya akun? 
        {{-- <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500" wire:navigate>Daftar</a> --}}
    </div>
</div>