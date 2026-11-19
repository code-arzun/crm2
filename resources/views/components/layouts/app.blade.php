<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-900 flex overflow-hidden" x-data="{ sidebarOpen: false }">
    
    <!-- OVERLAY GELAP (Saat Sidebar Terbuka di HP) -->
    <div x-show="sidebarOpen" 
         x-transition.opacity 
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-zinc-900/50 z-40 md:hidden backdrop-blur-sm" 
         style="display: none;"></div>

    <!-- SIDEBAR -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-zinc-800 border-r border-zinc-200 dark:border-zinc-700 flex flex-col transform transition-transform duration-300 ease-in-out md:relative md:translate-x-0">
        
        <!-- Logo & Tombol Close (di HP) -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-zinc-200 dark:border-zinc-700">
            <span class="text-xl font-bold text-zinc-900 dark:text-white">CRM System</span>
            <button @click="sidebarOpen = false" class="md:hidden text-zinc-500 hover:text-red-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Menu -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                Dashboard
            </a>

            <!-- MENU KHUSUS ADMIN & SUPERADMIN -->
            @role('superadmin|admin')
                <div class="pt-4 pb-1 px-3 text-xs font-bold text-zinc-400 uppercase tracking-wider">Manajemen</div>
                <a href="{{ route('admin.orders') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.orders') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                    Semua Pesanan
                </a>
                <a href="{{ route('customers.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('customers.*') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                    Data Customer
                </a>
                <a href="{{ route('products.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('products.*') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                    Data Produk
                </a>
            @endrole

            <!-- MENU KHUSUS FINANCE -->
            @role('superadmin|admin|finance')
                <div class="pt-4 pb-1 px-3 text-xs font-bold text-zinc-400 uppercase tracking-wider">Keuangan</div>
                <a href="{{ route('payments.verify') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('payments.*') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                    Pembayaran
                </a>
                <a href="{{ route('returns.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('returns.*') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                    Manajemen Retur
                </a>
            @endrole

            <!-- MENU KHUSUS GUDANG -->
            @role('superadmin|admin|warehouse')
                <div class="pt-4 pb-1 px-3 text-xs font-bold text-zinc-400 uppercase tracking-wider">Gudang</div>
                <a href="{{ route('warehouse.shipments') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('warehouse.shipments') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                    Pengiriman
                </a>
                <a href="{{ route('warehouse.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('warehouse.index') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                    Stok Gudang
                </a>
                <a href="{{ route('warehouse.logs') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('warehouse.logs') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                    Riwayat Stok
                </a>
                <a href="{{ route('returns.receive') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('returns.receive') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                    Penerimaan Retur
                </a>
            @endrole

            <!-- MENU KHUSUS CUSTOMER -->
            @role('customer')
                <div class="pt-4 pb-1 px-3 text-xs font-bold text-zinc-400 uppercase tracking-wider">Belanja</div>
                <a href="{{ route('customer.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('customer.dashboard') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                    Halaman Utama
                </a>
                <a href="{{ route('catalog.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('catalog.*') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                    Katalog Produk
                </a>
                <a href="{{ route('orders.create') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('orders.create') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                    Keranjang / Checkout
                </a>
                <a href="{{ route('orders.my') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('orders.my', 'orders.show') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                    Pesanan Saya
                </a>
                <a href="{{ route('customers.lengkapi') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('customer.lengkapi') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                    Profil Perusahaan
                </a>
            @endrole

            <!-- MENU SUPERADMIN -->
            @role('superadmin')
                <div class="pt-4 pb-1 px-3 text-xs font-bold text-zinc-400 uppercase tracking-wider">Sistem</div>
                <a href="{{ route('users.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('users.*') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                    Manajemen Staf
                </a>
                <a href="{{ route('settings.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('settings.*') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                    Pengaturan
                </a>
            @endrole
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
        <!-- HEADER -->
        <header class="h-16 bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between px-4 md:px-6">
            
            <!-- Tombol Hamburger (Hanya Muncul di HP) -->
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="md:hidden text-zinc-500 hover:text-indigo-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400 hidden md:block">
                    {{ $title ?? 'Dashboard' }}
                </div>
            </div>

            <!-- Profil & Logout -->
            <div class="flex items-center space-x-4 md:space-x-6">
                @role('customer')
                    <livewire:cart-icon />
                @endrole

                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-zinc-900 dark:text-white hidden md:block">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-500 font-medium">Logout</button>
                    </form>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <main class="flex-1 p-4 md:p-6 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>

    <!-- GLOBAL TOAST NOTIFICATION -->
    <div 
        x-data="{ show: false, message: '' }" 
        x-on:notify.window="
            message = $event.detail[0]?.message || $event.detail.message; 
            show = true; 
            setTimeout(() => show = false, 3000)
        "
        x-show="show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed bottom-6 right-6 z-[200] bg-green-600 text-white px-6 py-3 rounded-lg shadow-xl flex items-center gap-3" 
        style="display: none;"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span x-text="message" class="font-medium text-sm"></span>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>