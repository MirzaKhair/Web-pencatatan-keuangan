<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Transaksi</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola pemasukan dan pengeluaran Anda</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div x-data class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Message --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            {{-- Tombol Aksi + Modal --}}
            <div x-data="addTransactionModal()">
                <div class="flex flex-wrap gap-3">
                    <button @click="open('income')"
                            type="button"
                            class="btn-primary">
                        <x-lucide-plus class="w-4 h-4" />
                        Tambah Pemasukan
                    </button>
                    <button @click="open('expense')"
                            type="button"
                            class="btn-danger">
                        <x-lucide-plus class="w-4 h-4" />
                        Tambah Pengeluaran
                    </button>
                </div>

                {{-- Modal Backdrop --}}
                <div x-show="show"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40"
                     @click="close()"
                     style="display: none;">
                </div>

                {{-- Modal Content --}}
                <div x-show="show"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="fixed inset-0 z-50 overflow-y-auto"
                     style="display: none;">
                    <div class="flex min-h-full items-center justify-center p-4">
                        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 border border-gray-200" @click.stop>

                            {{-- Header --}}
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Tambah <span x-text="type === 'income' ? 'Pemasukan' : 'Pengeluaran'"></span>
                                </h3>
                                <button @click="close()" type="button" class="text-gray-400 hover:text-gray-600">
                                    <x-lucide-x class="w-5 h-5" />
                                </button>
                            </div>

                            {{-- Form --}}
                            <form @submit.prevent="submit()">
                                {{-- Field: Amount --}}
                                <div class="mb-4">
                                    <label for="amount" class="form-label">Nominal</label>
                                    <input id="amount"
                                           type="number"
                                           step="0.01"
                                           min="0.01"
                                           x-model="form.amount"
                                           placeholder="0"
                                           class="form-input"
                                           required>
                                    <template x-if="errors.amount">
                                        <p x-text="errors.amount[0]" class="form-error"></p>
                                    </template>
                                </div>

                                {{-- Field: Category --}}
                                <div class="mb-4">
                                    <label class="form-label">Kategori</label>
                                    <template x-if="categories.length > 0">
                                        <select x-model="form.category_id"
                                                class="form-select"
                                                required>
                                            <option value="">Pilih Kategori</option>
                                            <template x-for="cat in categories" :key="cat.id">
                                                <option :value="cat.id" x-text="cat.name"></option>
                                            </template>
                                        </select>
                                    </template>
                                    <template x-if="categories.length === 0">
                                        <div class="form-input bg-gray-50 text-gray-500 cursor-not-allowed">
                                            Tidak ada kategori tersedia.
                                        </div>
                                    </template>
                                    <template x-if="errors.category_id">
                                        <p x-text="errors.category_id[0]" class="form-error"></p>
                                    </template>
                                </div>

                                {{-- Field: Date --}}
                                <div class="mb-4">
                                    <label for="transaction_date" class="form-label">Tanggal</label>
                                    <input id="transaction_date"
                                           type="date"
                                           max="{{ now()->format('Y-m-d') }}"
                                           x-model="form.transaction_date"
                                           class="form-input"
                                           required>
                                    <template x-if="errors.transaction_date">
                                        <p x-text="errors.transaction_date[0]" class="form-error"></p>
                                    </template>
                                </div>

                                {{-- Field: Note --}}
                                <div class="mb-6">
                                    <label for="note" class="form-label">Catatan</label>
                                    <textarea id="note"
                                              x-model="form.note"
                                              rows="2"
                                              maxlength="255"
                                              placeholder="Opsional"
                                              class="form-input"></textarea>
                                    <template x-if="errors.note">
                                        <p x-text="errors.note[0]" class="form-error"></p>
                                    </template>
                                </div>

                                {{-- Tombol --}}
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="close()"
                                            class="btn-secondary">
                                        Batal
                                    </button>
                                    <button type="submit"
                                            :disabled="loading || categories.length === 0"
                                            class="disabled:opacity-50 disabled:cursor-not-allowed"
                                            :class="type === 'income' ? 'btn-primary' : 'btn-danger'">
                                        <span x-show="!loading">Simpan</span>
                                        <span x-show="loading">Menyimpan...</span>
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Edit Transaksi --}}
            <div x-data="editTransactionModal()"
                 @open-edit.window="open($event.detail)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50"
                 style="display: none;"
                 x-cloak>
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="close()"></div>
                {{-- Content --}}
                <div class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4">
                        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 border border-gray-200" @click.stop>

                            {{-- Header --}}
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Edit <span x-text="type === 'income' ? 'Pemasukan' : 'Pengeluaran'"></span>
                                </h3>
                                <button @click="close()" type="button" class="text-gray-400 hover:text-gray-600">
                                    <x-lucide-x class="w-5 h-5" />
                                </button>
                            </div>

                            {{-- Form --}}
                            <form @submit.prevent="submit()">
                                {{-- Field: Jenis --}}
                                <div class="mb-4">
                                    <label class="form-label mb-1">Jenis</label>
                                    <div class="flex gap-2">
                                        <button type="button" @click="if (type !== 'income') { type = 'income'; form.category_id = ''; }"
                                                class="flex-1 px-3 py-2 text-sm font-medium rounded-lg border transition"
                                                :class="type === 'income'
                                                    ? 'bg-green-50 border-green-300 text-green-700'
                                                    : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50'">
                                            Pemasukan
                                        </button>
                                        <button type="button" @click="if (type !== 'expense') { type = 'expense'; form.category_id = ''; }"
                                                class="flex-1 px-3 py-2 text-sm font-medium rounded-lg border transition"
                                                :class="type === 'expense'
                                                    ? 'bg-red-50 border-red-300 text-red-700'
                                                    : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50'">
                                            Pengeluaran
                                        </button>
                                    </div>
                                </div>

                                {{-- Field: Amount --}}
                                <div class="mb-4">
                                    <label for="edit_amount" class="form-label">Nominal</label>
                                    <input id="edit_amount"
                                           type="number"
                                           step="0.01"
                                           min="0.01"
                                           x-model="form.amount"
                                           placeholder="0"
                                           class="form-input"
                                           required>
                                    <template x-if="errors.amount">
                                        <p x-text="errors.amount[0]" class="form-error"></p>
                                    </template>
                                </div>

                                {{-- Field: Category --}}
                                <div class="mb-4">
                                    <label class="form-label">Kategori</label>
                                    <template x-if="categories.length > 0">
                                        <select x-model="form.category_id"
                                                class="form-select"
                                                required>
                                            <option value="">Pilih Kategori</option>
                                            <template x-for="cat in categories" :key="cat.id">
                                                <option :value="cat.id" x-text="cat.name"></option>
                                            </template>
                                        </select>
                                    </template>
                                    <template x-if="categories.length === 0">
                                        <div class="form-input bg-gray-50 text-gray-500 cursor-not-allowed">
                                            Tidak ada kategori tersedia.
                                        </div>
                                    </template>
                                    <template x-if="errors.category_id">
                                        <p x-text="errors.category_id[0]" class="form-error"></p>
                                    </template>
                                </div>

                                {{-- Field: Date --}}
                                <div class="mb-4">
                                    <label for="edit_transaction_date" class="form-label">Tanggal</label>
                                    <input id="edit_transaction_date"
                                           type="date"
                                           max="{{ now()->format('Y-m-d') }}"
                                           x-model="form.transaction_date"
                                           class="form-input"
                                           required>
                                    <template x-if="errors.transaction_date">
                                        <p x-text="errors.transaction_date[0]" class="form-error"></p>
                                    </template>
                                </div>

                                {{-- Field: Note --}}
                                <div class="mb-6">
                                    <label for="edit_note" class="form-label">Catatan</label>
                                    <textarea id="edit_note"
                                              x-model="form.note"
                                              rows="2"
                                              maxlength="255"
                                              placeholder="Opsional"
                                              class="form-input"></textarea>
                                    <template x-if="errors.note">
                                        <p x-text="errors.note[0]" class="form-error"></p>
                                    </template>
                                </div>

                                {{-- Tombol --}}
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="close()"
                                            class="btn-secondary">
                                        Batal
                                    </button>
                                    <button type="submit"
                                            :disabled="loading || categories.length === 0"
                                            class="disabled:opacity-50 disabled:cursor-not-allowed"
                                            :class="type === 'income' ? 'btn-primary' : 'btn-danger'">
                                        <span x-show="!loading">Simpan</span>
                                        <span x-show="loading">Menyimpan...</span>
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Konfirmasi Hapus --}}
            <div x-data="deleteConfirmModal()"
                 @confirm-delete.window="open($event.detail.id)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50"
                 style="display: none;"
                 x-cloak>
                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="close()"></div>
                <div class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4">
                        <div class="bg-white rounded-lg shadow-xl w-full max-w-sm p-6 border border-gray-200" @click.stop>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                    <x-lucide-alert-triangle class="w-5 h-5 text-red-600" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Hapus Transaksi</h3>
                                    <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus transaksi ini?</p>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3">
                                <button type="button" @click="close()"
                                        class="btn-secondary">
                                    Batal
                                </button>
                                <button type="button" @click="confirm()"
                                        :disabled="loading"
                                        class="btn-danger disabled:opacity-50">
                                    <span x-show="!loading">Hapus</span>
                                    <span x-show="loading">Menghapus...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter --}}
            <div class="card">
                <div class="card-body">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Filter
                    </h3>
                    <form method="GET" action="{{ route('transactions') }}">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="category_id" class="form-label">Kategori</label>
                                <select id="category_id"
                                        name="category_id"
                                        class="form-select">
                                    <option value="">Semua Kategori</option>
                                    @foreach($allCategories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="type" class="form-label">Jenis</label>
                                <select id="type"
                                        name="type"
                                        class="form-select">
                                    <option value="">Semua Jenis</option>
                                    <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                                </select>
                            </div>
                            <div>
                                <label for="date_from" class="form-label">Dari Tanggal</label>
                                <input id="date_from"
                                       type="date"
                                       name="date_from"
                                       value="{{ request('date_from') }}"
                                       max="{{ now()->format('Y-m-d') }}"
                                       class="form-input">
                            </div>
                            <div>
                                <label for="date_to" class="form-label">Sampai Tanggal</label>
                                <input id="date_to"
                                       type="date"
                                       name="date_to"
                                       value="{{ request('date_to') }}"
                                       max="{{ now()->format('Y-m-d') }}"
                                       class="form-input">
                            </div>
                        </div>
                        <div class="mt-4 flex gap-2">
                            <button type="submit"
                                    class="btn-primary">
                                <x-lucide-filter class="w-4 h-4" />
                                Terapkan Filter
                            </button>
                            <a href="{{ route('transactions') }}"
                               class="btn-secondary">
                                <x-lucide-x class="w-4 h-4" />
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Riwayat Transaksi --}}
            <div class="card">
                <div class="card-body">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Riwayat Transaksi
                    </h3>

                    @if ($transactions->isEmpty())
                        <div class="text-center py-8">
                            <x-lucide-inbox class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                            <p class="text-sm text-gray-500">
                                @if(request('category_id') || request('type') || request('date_from') || request('date_to'))
                                    Tidak ada transaksi yang sesuai dengan filter.
                                @else
                                    Belum ada transaksi.
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-[700px] w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-4 font-medium text-gray-500">Tanggal</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500">Kategori</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500">Jenis</th>
                                        <th class="text-right py-3 px-4 font-medium text-gray-500">Nominal</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500">Catatan</th>
                                        <th class="text-center py-3 px-4 font-medium text-gray-500">Aksi</th>
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
                                                    <span class="truncate">{{ $transaction->category_name }}</span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="badge {{ $transaction->category_type === 'income' ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $transaction->category_type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-right font-medium whitespace-nowrap
                                                {{ $transaction->category_type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $transaction->category_type === 'income' ? '+' : '-' }}{{ format_rupiah($transaction->amount) }}
                                            </td>
                                            <td class="py-3 px-4 text-gray-500 truncate">
                                                {{ $transaction->note ?: '-' }}
                                            </td>
                                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                                <div class="flex items-center justify-center gap-1">
                                                    <button type="button"
                                                            @click="$dispatch('open-edit', {
                                                                id: {{ $transaction->id }},
                                                                category_id: {{ $transaction->category_id }},
                                                                amount: {{ $transaction->amount }},
                                                                transaction_date: '{{ $transaction->transaction_date->format('Y-m-d') }}',
                                                                note: @js($transaction->note ?? ''),
                                                                type: '{{ $transaction->category_type }}'
                                                            })"
                                                            class="btn-icon">
                                                        <x-lucide-pencil class="w-4 h-4" />
                                                    </button>
                                                    <button type="button"
                                                            @click="$dispatch('confirm-delete', { id: {{ $transaction->id }} })"
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

                        <div class="mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('addTransactionModal', () => ({
            show: false,
            type: 'income',
            incomeCategories: @json($incomeCategories),
            expenseCategories: @json($expenseCategories),
            form: {
                amount: '',
                category_id: '',
                transaction_date: '{{ now()->format("Y-m-d") }}',
                note: '',
            },
            errors: {},
            loading: false,

            open(type) {
                this.type = type;
                this.form.amount = '';
                this.form.category_id = '';
                this.form.transaction_date = '{{ now()->format("Y-m-d") }}';
                this.form.note = '';
                this.errors = {};
                this.show = true;
            },

            close() {
                this.show = false;
                this.errors = {};
            },

            get categories() {
                return this.type === 'income'
                    ? this.incomeCategories
                    : this.expenseCategories;
            },

            async submit() {
                this.loading = true;
                this.errors = {};

                try {
                    const response = await fetch('{{ route("transactions.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.form),
                    });

                    if (response.ok) {
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

        Alpine.data('editTransactionModal', () => ({
            show: false,
            transactionId: null,
            type: 'income',
            incomeCategories: @json($incomeCategories),
            expenseCategories: @json($expenseCategories),
            form: {
                amount: '',
                category_id: '',
                transaction_date: '',
                note: '',
            },
            errors: {},
            loading: false,

            open(detail) {
                this.transactionId = detail.id;
                this.type = detail.type;
                this.form.amount = detail.amount;
                this.form.category_id = detail.category_id;
                this.form.transaction_date = detail.transaction_date;
                this.form.note = detail.note || '';
                this.errors = {};
                this.show = true;
            },

            close() {
                this.show = false;
                this.errors = {};
            },

            get categories() {
                return this.type === 'income'
                    ? this.incomeCategories
                    : this.expenseCategories;
            },

            onTypeChange() {
                this.form.category_id = '';
            },

            async submit() {
                this.loading = true;
                this.errors = {};
                const url = '{{ route("transactions.update", ":id") }}'.replace(':id', this.transactionId);

                try {
                    const response = await fetch(url, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify(this.form),
                        redirect: 'manual',
                    });

                    if (response.type === 'opaqueredirect' || response.ok) {
                        this.show = false;
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

        Alpine.data('deleteConfirmModal', () => ({
            show: false,
            transactionId: null,
            loading: false,

            open(id) {
                this.transactionId = id;
                this.show = true;
            },

            close() {
                this.show = false;
                this.transactionId = null;
            },

            async confirm() {
                this.loading = true;
                const url = '{{ route("transactions.destroy", ":id") }}'.replace(':id', this.transactionId);

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
                        this.show = false;
                        window.location.reload();
                    }
                } catch (error) {
                    console.error(error);
                } finally {
                    this.loading = false;
                }
            },
        }));
    });
    </script>
</x-app-layout>
