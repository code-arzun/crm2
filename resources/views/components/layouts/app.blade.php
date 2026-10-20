<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-900 flex">
    
    <!-- SIDEBAR -->
    <aside class="w-64 bg-white dark:bg-zinc-800 border-r border-zinc-200 dark:border-zinc-700 hidden md:flex flex-col">
        <!-- Logo -->
        <div class="h-16 flex items-center px-6 border-b border-zinc-200 dark:border-zinc-700">
            <span class="text-xl font-bold text-zinc-900 dark:text-white">CRM System</span>
        </div>

        <!-- Menu -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                Dashboard
            </a>
            @role('superadmin|admin|sales')
            <!-- Menu placeholder untuk fase berikutnya -->
                <a href="{{ route('customers.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('customers.index') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                    Customer
                </a>
            <a href="{{ route('products.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700">
                Produk
            </a>
            <a href="{{ route('admin.orders') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700">
                Kelola Pesanan (Admin)
            </a>
            <a href="{{ route('payments.verify') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('payments.verify') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                Verifikasi Pembayaran
            </a>
            @endrole
            @role('superadmin|admin|warehouse')
            <a href="{{ route('warehouse.shipments') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('warehouse.shipments') ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                Antrean Pengiriman
            </a>
            @endrole
            @role('customer')
            <a href="{{ route('catalog.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700">
                Katalog Produk
            </a>
            <a href="{{ route('orders.create') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700">
                Checkout
            </a>
            <a href="{{ route('orders.my') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-zinc-700">
                Pesanan Saya
            </a>
            @endrole
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- HEADER -->
        <header class="h-16 bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between px-6">
            <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                {{ $title ?? 'Dashboard' }}
            </div>

            <!-- Profil & Logout -->
            <div class="flex items-center space-x-6"> <!-- ubah space-x-4 jadi space-x-6 -->
                
                <!-- Tambahkan ini jika yang login adalah customer -->
                @role('customer')
                    <livewire:cart-icon />
                @endrole

                <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 hover:text-red-500 font-medium">Logout</button>
                </form>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <main class="flex-1 p-6">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>