<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Anggaran</h1>
            <p class="mt-1 text-sm text-gray-500">Atur dan pantau anggaran pengeluaran per kategori</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div x-data class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Message --}}
            @if(session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Filter Periode --}}
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('budgets') }}" class="flex flex-wrap items-end gap-4">
                        <div>
                            <label for="month" class="form-label">Bulan</label>
                            <select id="month" name="month" class="form-select">
                                @foreach($months as $num => $name)
                                    <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="year" class="form-label">Tahun</label>
                            <select id="year" name="year" class="form-select">
                                @foreach($years as $y)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn-primary">
                            <x-lucide-search class="w-4 h-4" />
                            Filter
                        </button>
                        <a href="{{ route('budgets') }}" class="btn-secondary">
                            <x-lucide-x class="w-4 h-4" />
                            Reset
                        </a>
                    </form>
                </div>
            </div>

            {{-- Tombol Tambah + Tabel --}}
            <div x-data="budgetModal()">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Anggaran</h3>
                    <button @click="open()"
                            type="button"
                            class="btn-primary">
                        <x-lucide-plus class="w-4 h-4" />
                        Tambah Anggaran
                    </button>
                </div>

                <div class="card">
                    <div class="card-body">
                        @if ($budgets->isEmpty())
                            <div class="text-center py-8">
                                <x-lucide-inbox class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                                <p class="text-sm text-gray-500">Belum ada anggaran untuk periode ini.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-[800px] w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-3 px-4 font-medium text-gray-500">Kategori</th>
                                            <th class="text-left py-3 px-4 font-medium text-gray-500">Periode</th>
                                            <th class="text-right py-3 px-4 font-medium text-gray-500">Anggaran</th>
                                            <th class="text-right py-3 px-4 font-medium text-gray-500">Terpakai</th>
                                            <th class="text-right py-3 px-4 font-medium text-gray-500">Sisa</th>
                                            <th class="text-center py-3 px-4 font-medium text-gray-500">Progress</th>
                                            <th class="text-center py-3 px-4 font-medium text-gray-500">Status</th>
                                            <th class="text-center py-3 px-4 font-medium text-gray-500">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($budgets as $budget)
                                            @php
                                                $used = $budget->used;
                                                $remaining = $budget->remaining;
                                                $progress = $budget->progress;
                                                $status = $budget->status;
                                            @endphp
                                            <tr class="table-row {{ $loop->last ? 'border-b-0' : '' }}">
                                                <td class="py-3 px-4">
                                                    <div class="flex items-center gap-2">
                                                        <x-lucide-{{ $budget->category->icon }} class="w-4 h-4 text-gray-400 shrink-0" />
                                                        <span>{{ $budget->category->name }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4">
                                                    {{ $months[$budget->month] }} {{ $budget->year }}
                                                </td>
                                                <td class="py-3 px-4 text-right font-medium">
                                                    {{ format_rupiah($budget->amount) }}
                                                </td>
                                                <td class="py-3 px-4 text-right font-medium text-red-600">
                                                    {{ format_rupiah($used) }}
                                                </td>
                                                <td class="py-3 px-4 text-right font-medium {{ $remaining >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ format_rupiah($remaining) }}
                                                </td>
                                                <td class="py-3 px-4">
                                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                                        <div class="h-2 rounded-full {{ $progress >= 100 ? 'bg-red-500' : ($progress >= 80 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                                             style="width: {{ min($progress, 100) }}%"></div>
                                                    </div>
                                                    <p class="text-xs text-gray-500 text-center mt-1">{{ number_format($progress, 0) }}%</p>
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    @if ($status === 'Aman')
                                                        <span class="badge-success">{{ $status }}</span>
                                                    @elseif ($status === 'Hampir Batas')
                                                        <span class="badge-warning">{{ $status }}</span>
                                                    @else
                                                        <span class="badge-danger">{{ $status }}</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                                    <div class="flex items-center justify-center gap-1">
                                                        <button type="button"
                                                                @click="$dispatch('open-edit-budget', {
                                                                    id: {{ $budget->id }},
                                                                    category_id: {{ $budget->category_id }},
                                                                    amount: {{ $budget->amount }},
                                                                    month: {{ $budget->month }},
                                                                    year: {{ $budget->year }}
                                                                })"
                                                                class="btn-icon">
                                                            <x-lucide-pencil class="w-4 h-4" />
                                                        </button>
                                                        <button type="button"
                                                                @click="$dispatch('confirm-delete-budget', { id: {{ $budget->id }} })"
                                                                class="btn-icon hover:!text-red-600 hover:!bg-red-50">
                                                            <x-lucide-trash-2 class="w-4 h-4" />
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Modal Tambah/Edit --}}
                <div x-show="show" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex min-h-full items-center justify-center p-4">
                        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="close()"></div>
                        <div class="bg-white rounded-lg shadow-xl border border-gray-200 w-full max-w-md p-6 relative" @click.stop>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900" x-text="isEdit ? 'Edit Anggaran' : 'Tambah Anggaran'"></h3>
                                <button @click="close()" class="text-gray-400 hover:text-gray-600">
                                    <x-lucide-x class="w-5 h-5" />
                                </button>
                            </div>

                            <form @submit.prevent="submit">
                                <div class="mb-4">
                                    <label class="form-label">Kategori</label>
                                    <select x-model="form.category_id"
                                            class="form-select">
                                        <option value="">Pilih Kategori</option>
                                        @foreach($expenseCategories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <template x-if="errors.category_id">
                                        <p class="form-error" x-text="errors.category_id[0]"></p>
                                    </template>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="form-label">Bulan</label>
                                        <select x-model="form.month"
                                                class="form-select">
                                            @foreach($months as $num => $name)
                                                <option value="{{ $num }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        <template x-if="errors.month">
                                            <p class="form-error" x-text="errors.month[0]"></p>
                                        </template>
                                    </div>
                                    <div>
                                        <label class="form-label">Tahun</label>
                                        <select x-model="form.year"
                                                class="form-select">
                                            @foreach($years as $y)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endforeach
                                        </select>
                                        <template x-if="errors.year">
                                            <p class="form-error" x-text="errors.year[0]"></p>
                                        </template>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <label class="form-label">Nominal Anggaran</label>
                                    <input type="number"
                                           x-model="form.amount"
                                           min="1"
                                           class="form-input"
                                           placeholder="0">
                                    <template x-if="errors.amount">
                                        <p class="form-error" x-text="errors.amount[0]"></p>
                                    </template>
                                </div>

                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="close()"
                                            class="btn-secondary">
                                        Batal
                                    </button>
                                    <button type="submit" :disabled="loading"
                                            class="btn-primary disabled:opacity-50">
                                        <span x-text="isEdit ? 'Simpan' : 'Tambah'"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Modal Hapus --}}
    <div x-data="deleteBudgetModal()">
        <template x-if="showDelete">
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeDelete()"></div>
                    <div class="bg-white rounded-lg shadow-xl border border-gray-200 w-full max-w-md p-6 relative" @click.stop>
                        <div class="flex items-center justify-center mb-4">
                            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                                <x-lucide-alert-triangle class="w-6 h-6 text-red-600" />
                            </div>
                        </div>
                        <div class="text-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Hapus Anggaran</h3>
                            <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus anggaran ini?</p>
                        </div>
                        <div class="flex justify-center gap-3">
                            <button type="button" @click="closeDelete()"
                                    class="btn-secondary">
                                Batal
                            </button>
                            <button type="button" @click="confirmDelete()" :disabled="deleteLoading"
                                    class="btn-danger disabled:opacity-50">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('budgetModal', () => ({
            show: false,
            isEdit: false,
            budgetId: null,
            form: {
                category_id: '',
                amount: '',
                month: '{{ $month }}',
                year: '{{ $year }}',
            },
            errors: {},
            loading: false,

            open() {
                this.isEdit = false;
                this.budgetId = null;
                this.form.category_id = '';
                this.form.amount = '';
                this.form.month = '{{ $month }}';
                this.form.year = '{{ $year }}';
                this.errors = {};
                this.show = true;
            },

            close() {
                this.show = false;
                this.errors = {};
            },

            async submit() {
                this.loading = true;
                this.errors = {};

                const url = this.isEdit
                    ? '{{ route("budgets.update", ":id") }}'.replace(':id', this.budgetId)
                    : '{{ route("budgets.store") }}';
                const method = this.isEdit ? 'PUT' : 'POST';

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify(this.form),
                        redirect: 'manual',
                    });

                    if (response.type === 'opaqueredirect' || response.ok) {
                        window.location.reload();
                    } else if (response.status === 422) {
                        const data = await response.json();
                        this.errors = data.errors || {};
                    }
                } catch (error) {
                    console.error(error);
                } finally {
                    this.loading = false;
                }
            },
        }));

        Alpine.data('deleteBudgetModal', () => ({
            showDelete: false,
            deleteBudgetId: null,
            deleteLoading: false,

            init() {
                window.addEventListener('open-edit-budget', (e) => {
                    const modal = Alpine.$data(document.querySelector('[x-data="budgetModal()"]'));
                    modal.isEdit = true;
                    modal.budgetId = e.detail.id;
                    modal.form.category_id = e.detail.category_id;
                    modal.form.amount = e.detail.amount;
                    modal.form.month = e.detail.month;
                    modal.form.year = e.detail.year;
                    modal.errors = {};
                    modal.show = true;
                });

                window.addEventListener('confirm-delete-budget', (e) => {
                    this.deleteBudgetId = e.detail.id;
                    this.showDelete = true;
                });
            },

            closeDelete() {
                this.showDelete = false;
                this.deleteBudgetId = null;
            },

            async confirmDelete() {
                this.deleteLoading = true;
                const url = '{{ route("budgets.destroy", ":id") }}'.replace(':id', this.deleteBudgetId);

                try {
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        redirect: 'manual',
                    });

                    if (response.type === 'opaqueredirect' || response.ok) {
                        window.location.reload();
                    }
                } catch (error) {
                    console.error(error);
                } finally {
                    this.deleteLoading = false;
                }
            },
        }));
    });
    </script>
</x-app-layout>
