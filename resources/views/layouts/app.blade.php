<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gradient-to-br from-gray-50 to-gray-100">
<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-gradient-to-b from-[#7f1d1d] to-[#5a1515] shadow-2xl flex flex-col">
        <!-- Logo -->
        <div class="p-6 flex justify-center">
            <a href="{{ route('dashboard') }}" class="flex items-center justify-center">
                <x-application-logo class="h-16 w-auto text-[#ffb800] drop-shadow-2xl" />
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-5 overflow-y-auto">

            <div class="space-y-1">

                {{-- Dashboard --}}
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="w-full px-4 py-3 rounded-lg transition-all duration-300 hover:bg-[#ffb800]/20 hover:translate-x-1 text-white hover:text-[#ffb800] font-medium {{ request()->routeIs('dashboard') ? 'bg-[#ffb800] text-[#7f1d1d] shadow-lg' : '' }}">
                    <x-icon name="home" class="w-5 h-5 mr-3 inline-block" />
                    Dashboard
                </x-nav-link>

                {{-- Admin menu, hanya untuk role_id = 1 --}}
                @if(optional(Auth::user())->role_id === 1)
                    <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" class="w-full px-4 py-3 rounded-lg transition-all duration-300 hover:bg-[#ffb800]/20 hover:translate-x-1 text-white hover:text-[#ffb800] font-medium {{ request()->routeIs('admin.users.*') ? 'bg-[#ffb800] text-[#7f1d1d] shadow-lg' : '' }}">
                        <x-icon name="users" class="w-5 h-5 mr-3 inline-block" />
                        Anggota
                    </x-nav-link>

                    <x-nav-link :href="route('admin.forms.index')" :active="request()->routeIs('admin.forms.*')" class="w-full px-4 py-3 rounded-lg transition-all duration-300 hover:bg-[#ffb800]/20 hover:translate-x-1 text-white hover:text-[#ffb800] font-medium {{ request()->routeIs('admin.forms.*') ? 'bg-[#ffb800] text-[#7f1d1d] shadow-lg' : '' }}">
                        <x-icon name="clipboard-document-list" class="w-5 h-5 mr-3 inline-block" />
                        Form Builder
                    </x-nav-link>

                    <x-nav-link :href="route('admin.submission.table')" :active="request()->routeIs('admin.submission.*')" class="w-full px-4 py-3 rounded-lg transition-all duration-300 hover:bg-[#ffb800]/20 hover:translate-x-1 text-white hover:text-[#ffb800] font-medium {{ request()->routeIs('admin.submission.*') ? 'bg-[#ffb800] text-[#7f1d1d] shadow-lg' : '' }}">
                        <x-icon name="table-cells" class="w-5 h-5 mr-3 inline-block" />
                        Submission Table
                    </x-nav-link>

                    <x-nav-link :href="route('admin.import.form')" :active="request()->routeIs('import.form')" class="w-full px-4 py-3 rounded-lg transition-all duration-300 hover:bg-[#ffb800]/20 hover:translate-x-1 text-white hover:text-[#ffb800] font-medium {{ request()->routeIs('import.form') ? 'bg-[#ffb800] text-[#7f1d1d] shadow-lg' : '' }}">
                        <x-icon name="arrow-up-tray" class="w-5 h-5 mr-3 inline-block" />
                        Import GeoJSON
                    </x-nav-link>
                    
                    <x-nav-link :href="route('admin.laporan-validasi.index')" :active="request()->routeIs('admin.laporan-validasi.*')" class="w-full px-4 py-3 rounded-lg transition-all duration-300 hover:bg-[#ffb800]/20 hover:translate-x-1 text-white hover:text-[#ffb800] font-medium {{ request()->routeIs('admin.laporan-validasi.*') ? 'bg-[#ffb800] text-[#7f1d1d] shadow-lg' : '' }}">
                    <x-icon name="check-circle" class="w-5 h-5 mr-3 inline-block" />
                    Validasi
                </x-nav-link>
                @endif

                {{-- Admin menu, hanya untuk role_id = 2 --}}
                @if(optional(Auth::user())->role_id === 2)

                    <x-nav-link :href="route('admin.submission.table')" :active="request()->routeIs('admin.submission.*')" class="w-full px-4 py-3 rounded-lg transition-all duration-300 hover:bg-[#ffb800]/20 hover:translate-x-1 text-white hover:text-[#ffb800] font-medium {{ request()->routeIs('admin.submission.*') ? 'bg-[#ffb800] text-[#7f1d1d] shadow-lg' : '' }}">
                        <x-icon name="table-cells" class="w-5 h-5 mr-3 inline-block" />
                        Submission Table
                    </x-nav-link>

                    <x-nav-link :href="route('admin.import.form')" :active="request()->routeIs('import.form')" class="w-full px-4 py-3 rounded-lg transition-all duration-300 hover:bg-[#ffb800]/20 hover:translate-x-1 text-white hover:text-[#ffb800] font-medium {{ request()->routeIs('import.form') ? 'bg-[#ffb800] text-[#7f1d1d] shadow-lg' : '' }}">
                        <x-icon name="arrow-up-tray" class="w-5 h-5 mr-3 inline-block" />
                        Import GeoJSON
                    </x-nav-link>
                @endif

                {{-- Admin menu, hanya untuk role_id = 3 --}}
                @if(optional(Auth::user())->role_id === 3)

                <x-nav-link :href="route('admin.laporan-validasi.index')" :active="request()->routeIs('admin.laporan-validasi.*')" class="w-full px-4 py-3 rounded-lg transition-all duration-300 hover:bg-[#ffb800]/20 hover:translate-x-1 text-white hover:text-[#ffb800] font-medium {{ request()->routeIs('admin.laporan-validasi.*') ? 'bg-[#ffb800] text-[#7f1d1d] shadow-lg' : '' }}">
                    <x-icon name="check-circle" class="w-5 h-5 mr-3 inline-block" />
                    Validasi
                </x-nav-link>

                @endif

                {{-- General menu --}}
                <x-nav-link :href="route('peta.index')" :active="request()->routeIs('peta.*')" class="w-full px-4 py-3 rounded-lg transition-all duration-300 hover:bg-[#ffb800]/20 hover:translate-x-1 text-white hover:text-[#ffb800] font-medium {{ request()->routeIs('peta.*') ? 'bg-[#ffb800] text-[#7f1d1d] shadow-lg' : '' }}">
                    <x-icon name="map" class="w-5 h-5 mr-3 inline-block" />
                    Peta
                </x-nav-link>

                <x-nav-link :href="route('forms.list')" :active="request()->routeIs('forms.list') || request()->routeIs('forms.show')" class="w-full px-4 py-3 rounded-lg transition-all duration-300 hover:bg-[#ffb800]/20 hover:translate-x-1 text-white hover:text-[#ffb800] font-medium {{ request()->routeIs('forms.list') || request()->routeIs('forms.show') ? 'bg-[#ffb800] text-[#7f1d1d] shadow-lg' : '' }}">
                    <x-icon name="document-text" class="w-5 h-5 mr-3 inline-block" />
                    Form
                </x-nav-link>

                <x-nav-link :href="route('laporan.create')" :active="request()->routeIs('laporan.*')" class="w-full px-4 py-3 rounded-lg transition-all duration-300 hover:bg-[#ffb800]/20 hover:translate-x-1 text-white hover:text-[#ffb800] font-medium {{ request()->routeIs('laporan.*') ? 'bg-[#ffb800] text-[#7f1d1d] shadow-lg' : '' }}">
                    <x-icon name="pencil-square" class="w-5 h-5 mr-3 inline-block" />
                    Laporan
                </x-nav-link>

            </div>

        </nav>
        <!-- User Footer -->
        <div class="mt-auto p-4 border-t border-[#ffb800]/30 bg-[#5a1515]/50">
            <div class="text-sm text-[#ffb800] mb-3 font-semibold flex items-center">
                <div class="w-8 h-8 rounded-full bg-[#ffb800] text-[#7f1d1d] flex items-center justify-center font-bold mr-2">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <span class="text-white">{{ Auth::user()->name }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex items-center text-[#ffb800] hover:text-white text-sm font-medium transition-all duration-300 hover:translate-x-1 w-full px-3 py-2 rounded-lg hover:bg-[#ffb800]/20">
                    <x-icon name="arrow-right-on-rectangle" class="w-5 h-5 mr-2" />
                    Log Out
                </button>
            </form>
        </div>
    </aside>

    <!-- Page Content -->
    <div class="flex-1 flex flex-col">
        @isset($header)
            <header class="bg-gradient-to-r from-[#7f1d1d] to-[#5a1515] shadow-lg">
                <div class="py-6 px-6 text-xl font-bold text-white flex items-center">
                    <div class="w-1 h-8 bg-[#ffb800] mr-4 rounded-full"></div>
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="p-6 bg-gradient-to-br from-gray-50 to-gray-100">
            <div class="animate-fade-in-up">
                {{ $slot }}
            </div>
        </main>
    </div>

</div>
</body>
</html>
