{{-- Mobile Top Bar + Desktop Sidebar Wrapper (shared Alpine scope) --}}
<div x-data="{ sidebarOpen: false }"
     x-on:open-sidebar.window="sidebarOpen = true"
     x-on:close-sidebar.window="sidebarOpen = false"
     x-on:keydown.escape.window="sidebarOpen = false"
>

    {{-- Sidebar Desktop --}}
    <aside
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out
               lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    >
        {{-- Logo --}}
        <div class="flex items-center gap-3 h-16 px-6 border-b border-gray-200">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <x-application-logo class="w-8 h-8" />
                <span class="text-lg font-semibold text-gray-900">Keuangan</span>
            </a>
        </div>

        {{-- Menu Utama --}}
        <nav class="flex flex-col h-[calc(100vh-4rem)] p-4">
            <div class="flex-1 space-y-1">
                <a href="{{ route('dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : 'sidebar-link-default' }}">
                    <x-lucide-layout-dashboard class="w-5 h-5" />
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('transactions') }}"
                   class="sidebar-link {{ request()->routeIs('transactions') ? 'sidebar-link-active' : 'sidebar-link-default' }}">
                    <x-lucide-arrow-left-right class="w-5 h-5" />
                    <span>Transaksi</span>
                </a>

                <a href="{{ route('categories') }}"
                   class="sidebar-link {{ request()->routeIs('categories') ? 'sidebar-link-active' : 'sidebar-link-default' }}">
                    <x-lucide-tag class="w-5 h-5" />
                    <span>Kategori</span>
                </a>

                <a href="{{ route('budgets') }}"
                   class="sidebar-link {{ request()->routeIs('budgets') ? 'sidebar-link-active' : 'sidebar-link-default' }}">
                    <x-lucide-wallet class="w-5 h-5" />
                    <span>Anggaran</span>
                </a>

                <a href="{{ route('reports') }}"
                   class="sidebar-link {{ request()->routeIs('reports') ? 'sidebar-link-active' : 'sidebar-link-default' }}">
                    <x-lucide-bar-chart-3 class="w-5 h-5" />
                    <span>Laporan</span>
                </a>
            </div>

            {{-- Menu Bawah --}}
            <div class="border-t border-gray-200 pt-4 space-y-1">
                <a href="{{ route('profile.edit') }}"
                   class="sidebar-link {{ request()->routeIs('profile.*') ? 'sidebar-link-active' : 'sidebar-link-default' }}">
                    <x-lucide-settings class="w-5 h-5" />
                    <span>Pengaturan</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="sidebar-link sidebar-link-default w-full text-left">
                        <x-lucide-log-out class="w-5 h-5" />
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    {{-- Mobile Top Bar --}}
    <div class="lg:hidden fixed top-0 left-0 right-0 z-40 bg-white border-b border-gray-200 h-16 flex items-center px-4">
        <button
            x-on:click="sidebarOpen = true"
            class="btn-icon"
        >
            <x-lucide-menu class="w-5 h-5" />
        </button>

        <div class="flex items-center gap-3 ml-3">
            <x-application-logo class="w-8 h-8" />
            <span class="text-lg font-semibold text-gray-900">Keuangan</span>
        </div>

        <div class="ml-auto">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="btn-icon">
                        <x-lucide-user class="w-5 h-5" />
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="px-4 py-2 border-b border-gray-100">
                        <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                    <x-dropdown-link :href="route('profile.edit')">
                        <x-lucide-settings class="w-4 h-4 mr-2" />
                        {{ __('Pengaturan') }}
                    </x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            <x-lucide-log-out class="w-4 h-4 mr-2" />
                            {{ __('Logout') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>

    {{-- Mobile Sidebar Backdrop --}}
    <div
        x-show="sidebarOpen"
        x-on:click="sidebarOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden"
        style="display: none;"
    ></div>

</div>
