<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Ringkasan aktivitas keuangan Anda</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- FILTER PERIODE --}}
            <div class="flex flex-wrap gap-2">
                @php
                    $periods = [
                        'today' => 'Hari Ini',
                        'week'  => 'Minggu Ini',
                        'month' => 'Bulan Ini',
                        'year'  => 'Tahun Ini',
                        'all'   => 'Semua',
                    ];
                @endphp

                @foreach ($periods as $key => $label)
                    <a href="{{ route('dashboard', ['period' => $key]) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out
                              {{ $period === $key
                                 ? 'bg-green-600 text-white'
                                 : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- KARTU STATISTIK --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Saldo Saat Ini --}}
                <div class="card card-body">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <x-lucide-wallet class="w-6 h-6 text-blue-600" />
                        </div>
                        <div class="ms-4">
                            <p class="text-sm text-gray-500">Saldo Saat Ini</p>
                            <p class="text-2xl font-semibold text-gray-900">
                                {{ format_rupiah($currentBalance) }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Total Pemasukan --}}
                <div class="card card-body">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-50 rounded-lg">
                            <x-lucide-trending-up class="w-6 h-6 text-green-600" />
                        </div>
                        <div class="ms-4">
                            <p class="text-sm text-gray-500">Total Pemasukan</p>
                            <p class="text-2xl font-semibold text-green-600">
                                {{ format_rupiah($totalIncome) }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Total Pengeluaran --}}
                <div class="card card-body">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-50 rounded-lg">
                            <x-lucide-trending-down class="w-6 h-6 text-red-600" />
                        </div>
                        <div class="ms-4">
                            <p class="text-sm text-gray-500">Total Pengeluaran</p>
                            <p class="text-2xl font-semibold text-red-600">
                                {{ format_rupiah($totalExpense) }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- GRAFIK KEUANGAN --}}
            <div class="card">
                <div class="card-body">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Grafik Keuangan
                    </h3>
                    <div class="h-[300px]">
                        <canvas id="financialChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- RINGKASAN KATEGORI --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Ringkasan Pemasukan --}}
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Pemasukan Terbesar
                        </h3>
                        @if ($incomeSummary->isEmpty())
                            <div class="text-center py-6">
                                <x-lucide-inbox class="w-10 h-10 mx-auto mb-2 text-gray-300" />
                                <p class="text-sm text-gray-500">Belum ada data pemasukan.</p>
                            </div>
                        @else
                            <div class="table-container">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-3 px-4 font-medium text-gray-500">Kategori</th>
                                            <th class="text-right py-3 px-4 font-medium text-gray-500">Total</th>
                                            <th class="text-center py-3 px-4 font-medium text-gray-500">Transaksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($incomeSummary as $item)
                                            <tr class="table-row {{ $loop->last ? 'border-b-0' : '' }}">
                                                <td class="py-3 px-4">
                                                    <div class="flex items-center gap-2">
                                                        <x-lucide-{{ $item->icon }} class="w-4 h-4 text-gray-400" />
                                                        <span>{{ $item->name }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4 text-right font-medium text-green-600">
                                                    {{ format_rupiah($item->total) }}
                                                </td>
                                                <td class="py-3 px-4 text-center text-gray-500">
                                                    {{ $item->transaction_count }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Ringkasan Pengeluaran --}}
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Pengeluaran Terbesar
                        </h3>
                        @if ($expenseSummary->isEmpty())
                            <div class="text-center py-6">
                                <x-lucide-inbox class="w-10 h-10 mx-auto mb-2 text-gray-300" />
                                <p class="text-sm text-gray-500">Belum ada data pengeluaran.</p>
                            </div>
                        @else
                            <div class="table-container">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-3 px-4 font-medium text-gray-500">Kategori</th>
                                            <th class="text-right py-3 px-4 font-medium text-gray-500">Total</th>
                                            <th class="text-center py-3 px-4 font-medium text-gray-500">Transaksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($expenseSummary as $item)
                                            <tr class="table-row {{ $loop->last ? 'border-b-0' : '' }}">
                                                <td class="py-3 px-4">
                                                    <div class="flex items-center gap-2">
                                                        <x-lucide-{{ $item->icon }} class="w-4 h-4 text-gray-400" />
                                                        <span>{{ $item->name }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4 text-right font-medium text-red-600">
                                                    {{ format_rupiah($item->total) }}
                                                </td>
                                                <td class="py-3 px-4 text-center text-gray-500">
                                                    {{ $item->transaction_count }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- RIWAYAT TRANSAKSI TERBARU --}}
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Riwayat Transaksi Terbaru
                        </h3>
                        <a href="{{ route('transactions') }}" class="btn-ghost text-sm">
                            Lihat Semua
                            <x-lucide-arrow-right class="w-4 h-4" />
                        </a>
                    </div>

                    @if ($transactions->isEmpty())
                        <div class="text-center py-8">
                            <x-lucide-inbox class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                            <p class="text-sm text-gray-500">Belum ada transaksi</p>
                        </div>
                    @else
                        <div class="table-container">
                            <table class="min-w-[600px] w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-4 font-medium text-gray-500">Tanggal</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500">Kategori</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500">Jenis</th>
                                        <th class="text-right py-3 px-4 font-medium text-gray-500">Nominal</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        <tr class="table-row {{ $loop->last ? 'border-b-0' : '' }}">
                                            <td class="py-3 px-4">
                                                {{ $transaction->transaction_date->format('d/m/Y') }}
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="flex items-center gap-2">
                                                    <x-lucide-{{ $transaction->category_icon }} class="w-4 h-4 text-gray-400" />
                                                    <span>{{ $transaction->category_name }}</span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="badge {{ $transaction->category_type === 'income' ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $transaction->category_type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-right font-medium
                                                {{ $transaction->category_type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $transaction->category_type === 'income' ? '+' : '-' }}{{ format_rupiah($transaction->amount) }}
                                            </td>
                                            <td class="py-3 px-4 text-gray-500">
                                                {{ $transaction->note ?: '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartData = @json($chartData);
        const period = @json($period);

        const labels = [...new Set(chartData.map(d => String(d.label)))].sort();

        const incomeData = labels.map(label => {
            const found = chartData.find(d => String(d.label) === label && d.category_type === 'income');
            return found ? parseFloat(found.total) : 0;
        });

        const expenseData = labels.map(label => {
            const found = chartData.find(d => String(d.label) === label && d.category_type === 'expense');
            return found ? parseFloat(found.total) : 0;
        });

        function formatLabel(label, period) {
            if (period === 'today') {
                return label + ':00';
            }
            if (period === 'year') {
                const months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                return months[parseInt(label)] || label;
            }
            if (period === 'all') {
                const parts = label.split('-');
                return parts[1] + '/' + parts[0];
            }
            if (period === 'week' || period === 'month') {
                const parts = label.split('-');
                return parts[2] + '/' + parts[1];
            }
            return label;
        }

        const formattedLabels = labels.map(l => formatLabel(l, period));

        const ctx = document.getElementById('financialChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: formattedLabels,
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: incomeData,
                        backgroundColor: 'rgba(34, 197, 94, 0.5)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1
                    },
                    {
                        label: 'Pengeluaran',
                        data: expenseData,
                        backgroundColor: 'rgba(239, 68, 68, 0.5)',
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return (value / 1000000).toFixed(0) + 'jt';
                                }
                                if (value >= 1000) {
                                    return (value / 1000).toFixed(0) + 'rb';
                                }
                                return value;
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' +
                                       new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
