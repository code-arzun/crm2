<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-900 flex flex-col">
    
    <!-- NAVBAR PUBLIK (Responsive) -->
    <header class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="text-lg md:text-xl font-bold text-indigo-600 dark:text-indigo-400 truncate max-w-[150px] md:max-w-none">
                    {{ \App\Models\Setting::get('company_name', 'Nama Perusahaan') }}
                </a>
                
                <!-- Menu Desktop -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-zinc-600 hover:text-indigo-600">Beranda</a>
                    <a href="{{ route('public.catalog') }}" class="text-sm font-medium text-zinc-600 hover:text-indigo-600">Katalog</a>
                    <a href="{{ route('home') }}#tentang" class="text-sm font-medium text-zinc-600 hover:text-indigo-600">Tentang Kami</a>
                    
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition">Dashboard</a>
                    @else
                        <!-- TOMBOL MASUK & DAFTAR (MEMICU MODAL) -->
                        <button onclick="window.dispatchEvent(new CustomEvent('open-guest-modal'))" class="text-sm font-bold text-zinc-900 dark:text-white hover:text-indigo-600 transition">Masuk</button>
                        <button onclick="window.dispatchEvent(new CustomEvent('open-guest-modal'))" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm">Daftar</button>
                    @endauth
                </div>

                <!-- Tombol Login/Daftar di HP -->
                <div class="flex md:hidden items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-indigo-600 text-white px-3 py-1.5 rounded text-xs font-bold">Dashboard</a>
                    @else
                        <!-- TOMBOL MASUK & DAFTAR (MEMICU MODAL) -->
                        <button onclick="window.dispatchEvent(new CustomEvent('open-guest-modal'))" class="text-xs font-bold text-zinc-600 dark:text-zinc-400">Masuk</button>
                        <button onclick="window.dispatchEvent(new CustomEvent('open-guest-modal'))" class="bg-indigo-600 text-white px-3 py-1.5 rounded text-xs font-bold">Daftar</button>
                    @endauth
                </div>
            </div>
        </div>
        
        <!-- Sub-Menu (Hanya di HP, Scroll Horizontal) -->
        <div class="md:hidden border-t border-zinc-100 dark:border-zinc-700 overflow-x-auto">
            <div class="flex px-4 py-2 gap-4 whitespace-nowrap">
                <a href="{{ route('home') }}" class="text-xs font-medium text-zinc-600">Beranda</a>
                <a href="{{ route('public.catalog') }}" class="text-xs font-medium text-zinc-600">Katalog</a>
                <a href="{{ route('home') }}#tentang" class="text-xs font-medium text-zinc-600">Tentang</a>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- FOOTER -->
    <footer class="bg-white dark:bg-zinc-800 border-t border-zinc-200 dark:border-zinc-700 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-zinc-500">
            &copy; {{ date('Y') }} {{ \App\Models\Setting::get('company_name') }}. All rights reserved.
        </div>
    </footer>

    @include('partials.guest-modal')

    @livewireScripts
    @stack('scripts')
</body>
</html>