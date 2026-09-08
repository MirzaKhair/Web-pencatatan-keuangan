<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Laporan</h1>
            <p class="mt-1 text-sm text-gray-500">Lihat ringkasan dan detail transaksi berdasarkan periode</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Message --}}
            @if(session('error'))
                <div class="alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            {{-- FILTER --}}
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('reports') }}">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="date_from" class="form-label">Dari Tanggal</label>
                                <input id="date_from"
                                       type="date"
                                       name="date_from"
                                       value="{{ $dateFrom }}"
                                       max="{{ now()->format('Y-m-d') }}"
                                       class="form-input">
                            </div>
                            <div>
                                <label for="date_to" class="form-label">Sampai Tanggal</label>
                                <input id="date_to"
                                       type="date"
                                       name="date_to"
                                       value="{{ $dateTo }}"
                                       max="{{ now()->format('Y-m-d') }}"
                                       class="form-input">
                            </div>
                            <div>
                                <label for="type" class="form-label">Jenis Transaksi</label>
                                <select id="type"
                                        name="type"
                                        class="form-select">
                                    <option value="">Semua Jenis</option>
                                    <option value="income" {{ $type === 'income' ? 'selected' : '' }}>Pemasukan</option>
                                    <option value="expense" {{ $type === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                                </select>
                            </div>
                            <div>
                                <label for="category_id" class="form-label">Kategori</label>
                                <select id="category_id"
                                        name="category_id"
                                        class="form-select">
                                    <option value="">Semua Kategori</option>
                                    @foreach($allCategories as $category)
                                        <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 flex gap-2">
                            <button type="submit" class="btn-primary">
                                <x-lucide-filter class="w-4 h-4" />
                                Terapkan Filter
                            </button>
                            <a href="{{ route('reports') }}" class="btn-secondary">
                                <x-lucide-x class="w-4 h-4" />
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- RINGKASAN --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card card-body">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-full">
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

                <div class="card card-body">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-100 rounded-full">
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

                <div class="card card-body">
                    <div class="flex items-center">
                        <div class="p-3 {{ $difference >= 0 ? 'bg-blue-100' : 'bg-red-100' }} rounded-full">
                            <x-lucide-wallet class="w-6 h-6 {{ $difference >= 0 ? 'text-blue-600' : 'text-red-600' }}" />
                        </div>
                        <div class="ms-4">
                            <p class="text-sm text-gray-500">Selisih</p>
                            <p class="text-2xl font-semibold {{ $difference >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                {{ format_rupiah($difference) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DETAIL TRANSAKSI --}}
            <div class="card">
                <div class="card-body">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Detail Transaksi
                    </h3>

                    @if ($transactions->isEmpty())
                        <div class="text-center py-8">
                            <x-lucide-inbox class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                            <p class="text-sm text-gray-500">Tidak ada transaksi pada periode atau filter yang dipilih.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-[600px] w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-4 font-medium text-gray-500">Tanggal</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500">Kategori</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500">Jenis</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500">Catatan</th>
                                        <th class="text-right py-3 px-4 font-medium text-gray-500">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        <tr class="table-row {{ $loop->last ? 'border-b-0' : '' }}">
                                            <td class="py-3 px-4 whitespace-nowrap">
                                                {{ $transaction->transaction_date->format('d/m/Y') }}
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="flex items-center gap-2">
                                                    <x-lucide-{{ $transaction->category_icon }} class="w-4 h-4 text-gray-400 shrink-0" />
                                                    <span>{{ $transaction->category_name }}</span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4">
                                                @if ($transaction->category_type === 'income')
                                                    <span class="badge-success">Pemasukan</span>
                                                @else
                                                    <span class="badge-danger">Pengeluaran</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-gray-500">
                                                {{ $transaction->note ?: '-' }}
                                            </td>
                                            <td class="py-3 px-4 text-right font-medium
                                                {{ $transaction->category_type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $transaction->category_type === 'income' ? '+' : '-' }}{{ format_rupiah($transaction->amount) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
