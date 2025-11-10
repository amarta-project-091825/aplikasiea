<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
            <svg class="w-6 h-6 mr-2 text-[#ffb800]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Form Pengaduan Laporan
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="welcome-gradient rounded-xl shadow-lg p-6 mb-8 text-white animate-fade-in-up">
                <div class="flex items-center">
                    <div class="bg-white/20 p-4 rounded-lg backdrop-blur-sm mr-4">
                        <svg class="w-8 h-8 text-[#ffb800]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold mb-2">Form Pengaduan Laporan</h1>
                        <p class="text-gray-200 text-sm md:text-base">
                            Sampaikan laporan atau pengaduan Anda kepada kami
                        </p>
                    </div>
                </div>
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

            <!-- Error Messages -->
            @if($errors->any())
                <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 dark:from-red-900/20 dark:to-rose-900/20 dark:border-red-800 rounded-xl p-4 animate-fade-in-up">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">Terdapat beberapa kesalahan:</h3>
                            <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300 space-y-1">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Container -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="p-8">
                    <form method="POST" action="{{ route('laporan.sendOtp') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                        @foreach($fields as $index => $field)
                            <div class="form-group animate-fade-in-up" style="animation-delay: {{ ($index + 3) * 0.1 }}s;">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-[#7f1d1d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    {{ $field['label'] }}
                                    @if($field['required'])
                                        <span class="ml-1 text-red-500">*</span>
                                    @endif
                                </label>

                                @if($field['type'] === 'text' || $field['type'] === 'number')
                                    <input type="{{ $field['type'] }}" 
                                           name="{{ $field['name'] }}" 
                                           value="{{ old($field['name']) }}"
                                           class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:border-[#7f1d1d] focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300 input-focus"
                                           placeholder="Masukkan {{ strtolower($field['label']) }}"
                                           {{ $field['required'] ? 'required' : '' }}>

                                @elseif($field['type'] === 'textarea')
                                    <textarea name="{{ $field['name'] }}" 
                                              rows="4"
                                              class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:border-[#7f1d1d] focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300 resize-none input-focus"
                                              placeholder="Masukkan {{ strtolower($field['label']) }}"
                                              {{ $field['required'] ? 'required' : '' }}>{{ old($field['name']) }}</textarea>

                                @elseif($field['type'] === 'select')
                                    <select name="{{ $field['name'] }}" 
                                            class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:border-[#7f1d1d] focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300 input-focus">
                                        @foreach($field['options'] as $opt)
                                            <option value="{{ $opt }}" {{ old($field['name']) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>

                                @elseif($field['type'] === 'radio')
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($field['options'] as $opt)
                                            <label class="flex items-center p-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:border-[#7f1d1d] hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-300">
                                                <input type="radio" 
                                                       name="{{ $field['name'] }}" 
                                                       value="{{ $opt }}" 
                                                       {{ old($field['name']) == $opt ? 'checked' : '' }}
                                                       class="w-5 h-5 text-[#7f1d1d] focus:ring-[#7f1d1d]/20 border-gray-300 dark:border-gray-600"
                                                       {{ $field['required'] ? 'required' : '' }}>
                                                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $opt }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                @elseif($field['type'] === 'file')
                                    <input type="file" 
                                           name="{{ $field['name'] }}" 
                                           class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-[#7f1d1d] file:to-[#5a1515] file:text-white hover:file:from-[#991b1b] hover:file:to-[#7f1d1d] transition-all duration-300"
                                           {{ $field['required'] ? 'required' : '' }}>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Maksimal 1MB
                                    </p>
                                @endif
                            </div>
                        @endforeach

                        <!-- Submit Button -->
                        <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-600">
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#7f1d1d] to-[#5a1515] text-white font-semibold rounded-lg hover:from-[#991b1b] hover:to-[#7f1d1d] focus:outline-none focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300 transform hover:scale-105 btn-hover">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Kirim OTP
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
