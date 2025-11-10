<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
            <svg class="w-6 h-6 mr-2 text-[#ffb800]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Laporan Berhasil
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <!-- Success Header -->
            <div class="text-center mb-8 animate-fade-in-up">
                <div class="bg-gradient-to-r from-green-100 to-emerald-100 dark:from-green-900/40 dark:to-emerald-900/40 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Laporan Berhasil Dikirim!</h1>
                <p class="text-gray-600 dark:text-gray-400">Terima kasih atas laporan Anda</p>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 px-6 py-4 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 dark:from-green-900/20 dark:to-emerald-900/20 dark:border-green-800 dark:text-green-200 animate-fade-in-up">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <!-- Info Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden animate-fade-in-up mb-6" style="animation-delay: 0.2s;">
                <div class="bg-gradient-to-r from-[#7f1d1d] to-[#5a1515] p-4">
                    <h3 class="text-white font-bold text-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Informasi Laporan
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-start">
                        <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-[#7f1d1d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Laporan ID</p>
                            <p class="font-bold text-gray-900 dark:text-white text-lg">{{ $laporanId }}</p>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex items-start">
                            <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg mr-4">
                                <svg class="w-6 h-6 text-[#7f1d1d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Kode Pelacakan</p>
                                <p class="font-bold text-gray-900 dark:text-white text-lg">{{ $trackingCode }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Simpan kode ini untuk melacak status laporan Anda</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <div class="text-center animate-fade-in-up" style="animation-delay: 0.3s;">
                <a href="{{ route('laporan.create') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#7f1d1d] to-[#5a1515] text-white font-semibold rounded-lg hover:from-[#991b1b] hover:to-[#7f1d1d] focus:outline-none focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300 transform hover:scale-105 btn-hover shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Buat Laporan Baru
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
