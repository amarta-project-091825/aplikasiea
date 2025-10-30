<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col">
        <!-- Logo -->
        <div class="p-5 flex justify-center border-b border-gray-200 dark:border-gray-700">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="h-9 w-auto text-gray-700 dark:text-gray-200" />
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-5">

            <div class="space-y-1">

                {{-- Dashboard --}}
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="w-full px-3 py-2 rounded transition hover:bg-gray-100">
                    <x-icon name="home" class="w-5 h-5 mr-2 inline-block" />
                    Dashboard
                </x-nav-link>

                {{-- Admin menu, hanya untuk role_id = 1 --}}
                @if(optional(Auth::user())->role_id === 1)
                    <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" class="w-full px-3 py-2 rounded transition hover:bg-gray-100">
                        <x-icon name="users" class="w-5 h-5 mr-2 inline-block" />
                        Anggota
                    </x-nav-link>

                    <x-nav-link :href="route('admin.forms.index')" :active="request()->routeIs('admin.forms.*')" class="w-full px-3 py-2 rounded transition hover:bg-gray-100">
                        <x-icon name="clipboard-document-list" class="w-5 h-5 mr-2 inline-block" />
                        Form Builder
                    </x-nav-link>

                    <x-nav-link :href="route('admin.submission.table')" :active="request()->routeIs('admin.submission.*')" class="w-full px-3 py-2 rounded transition hover:bg-gray-100">
                        <x-icon name="table-cells" class="w-5 h-5 mr-2 inline-block" />
                        Submission Table
                    </x-nav-link>

                    <x-nav-link :href="route('import.form')" :active="request()->routeIs('import.form')" class="w-full px-3 py-2 rounded transition hover:bg-gray-100">
                        <x-icon name="arrow-up-tray" class="w-5 h-5 mr-2 inline-block" />
                        Import GeoJSON
                    </x-nav-link>
                @endif

                {{-- General menu --}}
                <x-nav-link :href="route('peta.index')" :active="request()->routeIs('peta.*')" class="w-full px-3 py-2 rounded transition hover:bg-gray-100">
                    <x-icon name="map" class="w-5 h-5 mr-2 inline-block" />
                    Peta
                </x-nav-link>

                <x-nav-link :href="route('forms.list')" :active="request()->routeIs('forms.list') || request()->routeIs('forms.show')" class="w-full px-3 py-2 rounded transition hover:bg-gray-100">
                    <x-icon name="document-text" class="w-5 h-5 mr-2 inline-block" />
                    Form
                </x-nav-link>

                <x-nav-link :href="route('laporan.create')" :active="request()->routeIs('laporan.*')" class="w-full px-3 py-2 rounded transition hover:bg-gray-100">
                    <x-icon name="pencil-square" class="w-5 h-5 mr-2 inline-block" />
                    Laporan
                </x-nav-link>

                <x-nav-link :href="route('admin.laporan-validasi.index')" :active="request()->routeIs('admin.laporan-validasi.*')" class="w-full px-3 py-2 rounded transition hover:bg-gray-100">
                    <x-icon name="check-circle" class="w-5 h-5 mr-2 inline-block" />
                    Validasi
                </x-nav-link>

            </div>

        </nav>
        <!-- User Footer -->
        <div class="mt-8 p-4 border-t border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-700 dark:text-gray-300 mb-3 font-medium">
                {{ Auth::user()->name }}
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex items-center text-red-500 hover:text-red-600 text-sm font-medium">
                    <x-icon name="arrow-right-on-rectangle" class="w-5 h-5 mr-2" />
                    Log Out
                </button>
            </form>
        </div>
    </aside>

    <!-- Page Content -->
    <div class="flex-1 flex flex-col">
        @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="py-6 px-6 text-lg font-semibold text-gray-800 dark:text-gray-200">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="p-6">
            {{ $slot }}
        </main>
    </div>

</div>
</body>
</html>
