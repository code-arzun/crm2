<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Dashboard Analitik</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Ringkasan performa bisnis Anda.</p>
    </div>

    <!-- KARTU STATISTIK -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Pendapatan -->
        <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-500">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Total Pesanan -->
        <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-500">Total Pesanan</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">{{ $totalOrders }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Total Customer -->
        <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-500">Total Customer</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">{{ $totalCustomers }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Menunggu Verifikasi -->
        <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-500">Butuh Verifikasi</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingPayments }} Pembayaran</p>
                </div>
                <div class="bg-amber-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- GRAFIK PENJUALAN -->
        <div class="lg:col-span-2 bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Pendapatan 6 Bulan Terakhir</h3>
            <div class="relative h-72 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- PRODUK TERLARIS -->
        <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <h3 class="font-bold text-zinc-900 dark:text-white mb-4">Produk Terlaris</h3>
            <div class="space-y-4">
                @forelse($topProducts as $prod)
                    <div class="flex justify-between items-center border-b border-zinc-100 dark:border-zinc-700/50 pb-2 last:border-0">
                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300 truncate w-3/4">{{ $prod->nama_produk }}</span>
                        <span class="text-sm font-bold text-indigo-600">{{ $prod->total_qty }} Terjual</span>
                    </div>
                @empty
                    <p class="text-sm text-zinc-500 text-center py-4">Belum ada penjualan.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- STOK MENIPIS -->
    <div class="mt-6 bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-zinc-900 dark:text-white">Peringatan Stok Menipis (< 10)</h3>
            <a href="{{ route('warehouse.index') }}" class="text-sm text-indigo-600 hover:underline">Kelola Stok &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700 text-sm text-zinc-500">
                        <th class="py-2">Kode</th>
                        <th class="py-2">Nama Produk</th>
                        <th class="py-2 text-center">Sisa Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStocks as $stock)
                        <tr class="border-b border-zinc-100 dark:border-zinc-700/50">
                            <td class="py-3 text-xs font-mono text-zinc-500">{{ $stock->kode }}</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-white">{{ $stock->nama }}</td>
                            <td class="py-3 text-sm text-center">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full border {{ $stock->stok == 0 ? 'bg-red-100 text-red-800 border-red-200' : 'bg-amber-100 text-amber-800 border-amber-200' }}">
                                    {{ $stock->stok }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-sm text-zinc-500">Semua stok aman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- SCRIPT CHART.JS -->
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:navigated', () => {
            const ctx = document.getElementById('revenueChart');
            if (!ctx) return;

            const existingChart = Chart.getChart(ctx);
            if (existingChart) {
                existingChart.destroy();
            }

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! $chartLabels !!},
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: {!! $chartData !!},
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#4f46e5',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (value / 1000000) + 'jt';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</div>