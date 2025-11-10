<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Laporan Berhasil
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">

                @if(session('success'))
                    <p class="text-green-600 dark:text-green-400 mb-4">{{ session('success') }}</p>
                @endif

                <div class="space-y-2">
                    <p><strong>Laporan ID:</strong> {{ $laporanId }}</p>
                    <p><strong>Kode Pelacakan:</strong> {{ $trackingCode }}</p>
                </div>

                <a href="{{ route('laporan.create') }}"
                   class="mt-4 inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded">
                   Buat Laporan Baru
                </a>

            </div>
        </div>
    </div>
</x-app-layout>
