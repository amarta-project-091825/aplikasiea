<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
            <svg class="w-6 h-6 mr-2 text-[#ffb800]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Buat Form Baru
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="welcome-gradient rounded-xl shadow-lg p-6 mb-8 text-white animate-fade-in-up">
                <div class="flex items-center">
                    <div class="bg-white/20 p-4 rounded-lg backdrop-blur-sm mr-4">
                        <svg class="w-8 h-8 text-[#ffb800]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold mb-2">Buat Form Baru</h1>
                        <p class="text-gray-200 text-sm md:text-base">
                            Buat form kustom dengan berbagai tipe field untuk kebutuhan Anda
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Container -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="p-8">
                    <form method="post" action="{{ route('admin.forms.store') }}" enctype="multipart/form-data">
                        @csrf
                        @include('admin.forms._builder', ['form' => null])
                        <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200 dark:border-gray-600">
                            <a href="{{ route('admin.forms.index') }}"
                               class="inline-flex items-center px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Batal
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#7f1d1d] to-[#5a1515] text-white font-semibold rounded-lg hover:from-[#991b1b] hover:to-[#7f1d1d] focus:outline-none focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300 transform hover:scale-105 btn-hover">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan Form
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
