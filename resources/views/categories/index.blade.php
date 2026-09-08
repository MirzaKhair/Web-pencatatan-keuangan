<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Kategori</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola kategori pemasukan dan pengeluaran</p>
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

            {{-- Tombol Tambah --}}
            <div x-data="categoryModal()">
                <button @click="open('income')"
                        type="button"
                        class="btn-primary">
                    <x-lucide-plus class="w-4 h-4" />
                    Tambah Kategori
                </button>

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
                        <div class="bg-white rounded-lg shadow-xl border border-gray-200 w-full max-w-md p-6" @click.stop>

                            {{-- Header --}}
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900" x-text="isEdit ? 'Edit Kategori' : 'Tambah Kategori'"></h3>
                                <button @click="close()" class="text-gray-400 hover:text-gray-600">
                                    <x-lucide-x class="w-5 h-5" />
                                </button>
                            </div>

                            {{-- Form --}}
                            <form @submit.prevent="submit">
                                {{-- Nama --}}
                                <div class="mb-4">
                                    <label for="name" class="form-label">Nama</label>
                                    <input id="name"
                                           type="text"
                                           x-model="form.name"
                                           class="form-input"
                                           placeholder="Nama kategori">
                                    <template x-if="errors.name">
                                        <p class="form-error" x-text="errors.name[0]"></p>
                                    </template>
                                </div>

                                {{-- Jenis --}}
                                <div class="mb-4">
                                    <label for="type" class="form-label">Jenis</label>
                                    <select id="type"
                                            x-model="form.type"
                                            :disabled="isEdit"
                                            class="form-select disabled:bg-gray-50 disabled:cursor-not-allowed">
                                        <option value="income">Pemasukan</option>
                                        <option value="expense">Pengeluaran</option>
                                    </select>
                                    <template x-if="errors.type">
                                        <p class="form-error" x-text="errors.type[0]"></p>
                                    </template>
                                </div>

                                {{-- Icon --}}
                                <div class="mb-4">
                                    <label for="icon" class="form-label">Icon</label>
                                    <select id="icon"
                                            x-model="form.icon"
                                            class="form-select">
                                        <option value="">Pilih Icon</option>
                                        <optgroup label="Expense">
                                            <option value="utensils">utensils</option>
                                            <option value="cup-soda">cup-soda</option>
                                            <option value="bus">bus</option>
                                            <option value="car">car</option>
                                            <option value="shopping-cart">shopping-cart</option>
                                            <option value="gamepad-2">gamepad-2</option>
                                            <option value="graduation-cap">graduation-cap</option>
                                            <option value="heart-pulse">heart-pulse</option>
                                            <option value="house">house</option>
                                            <option value="lightbulb">lightbulb</option>
                                            <option value="smartphone">smartphone</option>
                                            <option value="shirt">shirt</option>
                                            <option value="plane">plane</option>
                                            <option value="package">package</option>
                                        </optgroup>
                                        <optgroup label="Income">
                                            <option value="banknote">banknote</option>
                                            <option value="wallet">wallet</option>
                                            <option value="briefcase-business">briefcase-business</option>
                                            <option value="gift">gift</option>
                                            <option value="trending-up">trending-up</option>
                                            <option value="landmark">landmark</option>
                                            <option value="circle-dollar-sign">circle-dollar-sign</option>
                                            <option value="piggy-bank">piggy-bank</option>
                                            <option value="coins">coins</option>
                                        </optgroup>
                                    </select>
                                    <template x-if="errors.icon">
                                        <p class="form-error" x-text="errors.icon[0]"></p>
                                    </template>
                                </div>

                                {{-- Tombol --}}
                                <div class="flex justify-end gap-3 mt-6">
                                    <button type="button"
                                            @click="close()"
                                            class="btn-secondary">
                                        Batal
                                    </button>
                                    <button type="submit"
                                            :disabled="loading"
                                            class="btn-primary disabled:opacity-50">
                                        <template x-if="loading">
                                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </template>
                                        <span x-text="isEdit ? 'Simpan' : 'Tambah'"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabs --}}
            <div x-data="{ activeTab: 'expense' }">
                <div class="border-b border-gray-200">
                    <nav class="flex gap-4">
                        <button @click="activeTab = 'expense'"
                                :class="activeTab === 'expense' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="py-2 px-1 border-b-2 text-sm font-medium transition">
                            Pengeluaran
                        </button>
                        <button @click="activeTab = 'income'"
                                :class="activeTab === 'income' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="py-2 px-1 border-b-2 text-sm font-medium transition">
                            Pemasukan
                        </button>
                    </nav>
                </div>

                {{-- Tab Content: Pengeluaran --}}
                <div x-show="activeTab === 'expense'" class="mt-4">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Kategori Pengeluaran</h3>

                            @if ($expenseCategories->isEmpty())
                                <div class="text-center py-8">
                                    <x-lucide-inbox class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                                    <p class="text-sm text-gray-500">Belum ada kategori pengeluaran.</p>
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-[500px] w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-gray-200">
                                                <th class="text-left py-3 px-4 font-medium text-gray-500">No</th>
                                                <th class="text-left py-3 px-4 font-medium text-gray-500">Nama</th>
                                                <th class="text-left py-3 px-4 font-medium text-gray-500">Icon</th>
                                                <th class="text-center py-3 px-4 font-medium text-gray-500">Transaksi</th>
                                                <th class="text-center py-3 px-4 font-medium text-gray-500">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($expenseCategories as $category)
                                                <tr class="table-row {{ $loop->last ? 'border-b-0' : '' }}">
                                                    <td class="py-3 px-4">{{ $loop->iteration }}</td>
                                                    <td class="py-3 px-4 font-medium">{{ $category->name }}</td>
                                                    <td class="py-3 px-4">
                                                        <div class="flex items-center gap-2">
                                                            <x-lucide-{{ $category->icon }} class="w-4 h-4 text-gray-400 shrink-0" />
                                                            <span class="text-gray-500 text-xs">{{ $category->icon }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-4 text-center">
                                                        <span class="badge-neutral">
                                                            {{ $category->transactions_count }}
                                                        </span>
                                                    </td>
                                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                                        <div class="flex items-center justify-center gap-1">
                                                            <button type="button"
                                                                    @click="$dispatch('open-edit-category', {
                                                                        id: {{ $category->id }},
                                                                        name: '{{ addslashes($category->name) }}',
                                                                        type: '{{ $category->type }}',
                                                                        icon: '{{ $category->icon }}'
                                                                    })"
                                                                    class="btn-icon">
                                                                <x-lucide-pencil class="w-4 h-4" />
                                                            </button>
                                                            <button type="button"
                                                                    @click="$dispatch('confirm-delete-category', { id: {{ $category->id }}, name: '{{ addslashes($category->name) }}', count: {{ $category->transactions_count }} })"
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
                </div>

                {{-- Tab Content: Pemasukan --}}
                <div x-show="activeTab === 'income'" class="mt-4">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Kategori Pemasukan</h3>

                            @if ($incomeCategories->isEmpty())
                                <div class="text-center py-8">
                                    <x-lucide-inbox class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                                    <p class="text-sm text-gray-500">Belum ada kategori pemasukan.</p>
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-[500px] w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-gray-200">
                                                <th class="text-left py-3 px-4 font-medium text-gray-500">No</th>
                                                <th class="text-left py-3 px-4 font-medium text-gray-500">Nama</th>
                                                <th class="text-left py-3 px-4 font-medium text-gray-500">Icon</th>
                                                <th class="text-center py-3 px-4 font-medium text-gray-500">Transaksi</th>
                                                <th class="text-center py-3 px-4 font-medium text-gray-500">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($incomeCategories as $category)
                                                <tr class="table-row {{ $loop->last ? 'border-b-0' : '' }}">
                                                    <td class="py-3 px-4">{{ $loop->iteration }}</td>
                                                    <td class="py-3 px-4 font-medium">{{ $category->name }}</td>
                                                    <td class="py-3 px-4">
                                                        <div class="flex items-center gap-2">
                                                            <x-lucide-{{ $category->icon }} class="w-4 h-4 text-gray-400 shrink-0" />
                                                            <span class="text-gray-500 text-xs">{{ $category->icon }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-4 text-center">
                                                        <span class="badge-neutral">
                                                            {{ $category->transactions_count }}
                                                        </span>
                                                    </td>
                                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                                        <div class="flex items-center justify-center gap-1">
                                                            <button type="button"
                                                                    @click="$dispatch('open-edit-category', {
                                                                        id: {{ $category->id }},
                                                                        name: '{{ addslashes($category->name) }}',
                                                                        type: '{{ $category->type }}',
                                                                        icon: '{{ $category->icon }}'
                                                                    })"
                                                                    class="btn-icon">
                                                                <x-lucide-pencil class="w-4 h-4" />
                                                            </button>
                                                            <button type="button"
                                                                    @click="$dispatch('confirm-delete-category', { id: {{ $category->id }}, name: '{{ addslashes($category->name) }}', count: {{ $category->transactions_count }} })"
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
                </div>
            </div>

        </div>
    </div>

    {{-- Modal Edit & Delete --}}
    <div x-data="categoryEditDeleteModal()">
        {{-- Edit Modal --}}
        <template x-if="showEdit">
            <div>
                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40" @click="closeEdit()"></div>
                <div class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4">
                        <div class="bg-white rounded-lg shadow-xl border border-gray-200 w-full max-w-md p-6" @click.stop>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Edit Kategori</h3>
                                <button @click="closeEdit()" class="text-gray-400 hover:text-gray-600">
                                    <x-lucide-x class="w-5 h-5" />
                                </button>
                            </div>

                            <form @submit.prevent="submitEdit">
                                <div class="mb-4">
                                    <label class="form-label">Nama</label>
                                    <input type="text"
                                           x-model="editForm.name"
                                           class="form-input">
                                    <template x-if="editErrors.name">
                                        <p class="form-error" x-text="editErrors.name[0]"></p>
                                    </template>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Jenis</label>
                                    <input type="text"
                                           :value="editForm.type === 'income' ? 'Pemasukan' : 'Pengeluaran'"
                                           disabled
                                           class="form-input bg-gray-50 cursor-not-allowed">
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Icon</label>
                                    <select x-model="editForm.icon"
                                            class="form-select">
                                        <option value="">Pilih Icon</option>
                                        <optgroup label="Expense">
                                            <option value="utensils">utensils</option>
                                            <option value="cup-soda">cup-soda</option>
                                            <option value="bus">bus</option>
                                            <option value="car">car</option>
                                            <option value="shopping-cart">shopping-cart</option>
                                            <option value="gamepad-2">gamepad-2</option>
                                            <option value="graduation-cap">graduation-cap</option>
                                            <option value="heart-pulse">heart-pulse</option>
                                            <option value="house">house</option>
                                            <option value="lightbulb">lightbulb</option>
                                            <option value="smartphone">smartphone</option>
                                            <option value="shirt">shirt</option>
                                            <option value="plane">plane</option>
                                            <option value="package">package</option>
                                        </optgroup>
                                        <optgroup label="Income">
                                            <option value="banknote">banknote</option>
                                            <option value="wallet">wallet</option>
                                            <option value="briefcase-business">briefcase-business</option>
                                            <option value="gift">gift</option>
                                            <option value="trending-up">trending-up</option>
                                            <option value="landmark">landmark</option>
                                            <option value="circle-dollar-sign">circle-dollar-sign</option>
                                            <option value="piggy-bank">piggy-bank</option>
                                            <option value="coins">coins</option>
                                        </optgroup>
                                    </select>
                                    <template x-if="editErrors.icon">
                                        <p class="form-error" x-text="editErrors.icon[0]"></p>
                                    </template>
                                </div>

                                <div class="flex justify-end gap-3 mt-6">
                                    <button type="button"
                                            @click="closeEdit()"
                                            class="btn-secondary">
                                        Batal
                                    </button>
                                    <button type="submit"
                                            :disabled="editLoading"
                                            class="btn-primary disabled:opacity-50">
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- Delete Confirm Modal --}}
        <template x-if="showDelete">
            <div>
                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40" @click="closeDelete()"></div>
                <div class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4">
                        <div class="bg-white rounded-lg shadow-xl border border-gray-200 w-full max-w-md p-6" @click.stop>
                            <div class="flex items-center justify-center mb-4">
                                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                                    <x-lucide-alert-triangle class="w-6 h-6 text-red-600" />
                                </div>
                            </div>

                            <div class="text-center mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Hapus Kategori</h3>
                                <p class="text-sm text-gray-500" x-text="deleteCount > 0 ? 'Kategori ini masih digunakan oleh ' + deleteCount + ' transaksi. Tidak dapat dihapus.' : 'Apakah Anda yakin ingin menghapus kategori ini?'"></p>
                            </div>

                            <div class="flex justify-center gap-3">
                                <button type="button"
                                        @click="closeDelete()"
                                        x-text="deleteCount > 0 ? 'Tutup' : 'Batal'"
                                        class="btn-secondary">
                                </button>
                                <button type="button"
                                        @click="confirmDelete()"
                                        :disabled="deleteLoading"
                                        x-show="deleteCount === 0"
                                        class="btn-danger disabled:opacity-50">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('categoryModal', () => ({
            show: false,
            isEdit: false,
            form: {
                name: '',
                type: 'income',
                icon: '',
            },
            errors: {},
            loading: false,

            open(type) {
                this.isEdit = false;
                this.form.name = '';
                this.form.type = type;
                this.form.icon = '';
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

                try {
                    const response = await fetch('{{ route("categories.store") }}', {
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

        Alpine.data('categoryEditDeleteModal', () => ({
            showEdit: false,
            showDelete: false,
            editCategoryId: null,
            editForm: {
                name: '',
                type: '',
                icon: '',
            },
            editErrors: {},
            editLoading: false,
            deleteCategoryId: null,
            deleteCount: 0,
            deleteLoading: false,

            init() {
                window.addEventListener('open-edit-category', (e) => {
                    this.editCategoryId = e.detail.id;
                    this.editForm.name = e.detail.name;
                    this.editForm.type = e.detail.type;
                    this.editForm.icon = e.detail.icon;
                    this.editErrors = {};
                    this.showEdit = true;
                });

                window.addEventListener('confirm-delete-category', (e) => {
                    this.deleteCategoryId = e.detail.id;
                    this.deleteCount = e.detail.count;
                    this.showDelete = true;
                });
            },

            closeEdit() {
                this.showEdit = false;
                this.editCategoryId = null;
                this.editErrors = {};
            },

            closeDelete() {
                this.showDelete = false;
                this.deleteCategoryId = null;
                this.deleteCount = 0;
            },

            async submitEdit() {
                this.editLoading = true;
                this.editErrors = {};
                const url = '{{ route("categories.update", ":id") }}'.replace(':id', this.editCategoryId);

                try {
                    const response = await fetch(url, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify(this.editForm),
                        redirect: 'manual',
                    });

                    if (response.type === 'opaqueredirect' || response.ok) {
                        this.showEdit = false;
                        window.location.reload();
                    } else if (response.status === 422) {
                        const data = await response.json();
                        this.editErrors = data.errors || {};
                    }
                } catch (error) {
                    console.error(error);
                } finally {
                    this.editLoading = false;
                }
            },

            async confirmDelete() {
                this.deleteLoading = true;
                const url = '{{ route("categories.destroy", ":id") }}'.replace(':id', this.deleteCategoryId);

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
                        this.showDelete = false;
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
