<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Setup Saldo Awal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-6 text-gray-600">
                        Tentukan saldo awal keuangan Anda. Data ini tidak dapat diubah setelah disimpan.
                    </p>

                    <form method="POST" action="{{ route('setup-initial-balance.store') }}">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="initial_balance" value="{{ __('Saldo Awal') }}" />
                            <input
                                id="initial_balance"
                                type="number"
                                step="0.01"
                                min="0"
                                name="initial_balance"
                                value="{{ old('initial_balance') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required
                                autofocus
                            />
                            <x-input-error :messages="$errors->get('initial_balance')" class="mt-1" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="initial_balance_date" value="{{ __('Tanggal Saldo Awal') }}" />
                            <input
                                id="initial_balance_date"
                                type="date"
                                max="{{ now()->format('Y-m-d') }}"
                                name="initial_balance_date"
                                value="{{ old('initial_balance_date', now()->format('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required
                            />
                            <x-input-error :messages="$errors->get('initial_balance_date')" class="mt-1" />
                        </div>

                        <div>
                            <x-primary-button class="w-full">
                                {{ __('Simpan Saldo Awal') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>