<div>
    <flux:heading size="xl" level="1">Daftar Akun Baru</flux:heading>
    <flux:subheading size="lg" class="mb-6">Hanya butuh 10 detik untuk memulai.</flux:subheading>

    <form wire:submit="register" class="space-y-4">
        <flux:input wire:model="name" label="Nama Lengkap" type="text" required autofocus />
        <flux:input wire:model="telepon" label="Nomor WhatsApp" type="text" required />
        <flux:input wire:model="email" label="Email" type="email" required />
        <flux:input wire:model="password" label="Password" type="password" required />
        <flux:input wire:model="password_confirmation" label="Konfirmasi Password" type="password" required />

        <flux:button variant="primary" type="submit" class="w-full">Daftar Sekarang</flux:button>
    </form>

    <div class="mt-6 text-center text-sm text-zinc-600 dark:text-zinc-400">
        Sudah punya akun? 
        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Masuk</a>
    </div>
</div>