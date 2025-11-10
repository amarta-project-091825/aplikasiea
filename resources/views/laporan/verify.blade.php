<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
            <svg class="w-6 h-6 mr-2 text-[#ffb800]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Verifikasi OTP
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="welcome-gradient rounded-xl shadow-lg p-6 mb-8 text-white animate-fade-in-up text-center">
                <div class="bg-white/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                    <svg class="w-8 h-8 text-[#ffb800]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold mb-2">Verifikasi OTP</h1>
                <p class="text-gray-200 text-sm md:text-base">
                    Masukkan kode OTP yang telah dikirim ke email Anda
                </p>
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

            <!-- Error Message -->
            @if(session('error'))
                <div class="mb-6 px-6 py-4 rounded-xl bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-800 dark:from-red-900/20 dark:to-rose-900/20 dark:border-red-800 dark:text-red-200 animate-fade-in-up">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <!-- OTP Form Container -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="p-8">
                    <form action="{{ route('laporan.verifyOtp') }}" method="POST" id="otpForm" class="space-y-6">
                        @csrf
                        <input type="hidden" name="otp_id" value="{{ $otp_id }}">

                        <div class="text-center mb-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Masukkan 6 digit kode OTP
                            </p>
                        </div>

                        {{-- Slot OTP 6 digit --}}
                        <div class="flex justify-center gap-3">
                            @for($i = 0; $i < 6; $i++)
                                <input type="text" name="otp[]" maxlength="1" pattern="\d" required
                                    class="w-14 h-14 text-center text-2xl font-bold border-2 border-gray-200 dark:border-gray-600 rounded-lg focus:border-[#7f1d1d] focus:ring-2 focus:ring-[#7f1d1d]/20 dark:bg-gray-700 dark:text-gray-200 otp-input transition-all duration-300 transform hover:scale-105">
                            @endfor
                        </div>

                        <button type="submit"
                            class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-[#7f1d1d] to-[#5a1515] text-white font-semibold rounded-lg hover:from-[#991b1b] hover:to-[#7f1d1d] focus:outline-none focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300 transform hover:scale-105 btn-hover">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Verifikasi OTP
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JS untuk auto-focus & gabungkan OTP --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('otpForm');
            const inputs = document.querySelectorAll('.otp-input');

            // Fokus slot pertama
            inputs[0].focus();

            inputs.forEach((input, idx) => {
                // pindah fokus ke slot berikutnya
                input.addEventListener('input', () => {
                    if (input.value.length === 1 && idx < inputs.length - 1) {
                        inputs[idx + 1].focus();
                    }
                });

                // backspace kembali
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && input.value === '' && idx > 0) {
                        inputs[idx - 1].focus();
                    }
                });
            });

            // Gabungkan OTP sebelum submit
            form.addEventListener('submit', (e) => {
                e.preventDefault(); // cegah default
                const otpValues = Array.from(inputs).map(i => i.value).join('');

                // Buat hidden input otp string
                let hiddenInput = form.querySelector('input[name="otp_hidden"]');
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'otp';
                    hiddenInput.id = 'otp_hidden';
                    form.appendChild(hiddenInput);
                }
                hiddenInput.value = otpValues;

                // hapus semua name slot untuk menghindari array
                inputs.forEach(i => i.removeAttribute('name'));

                // submit form
                form.submit();
            });
        });
    </script>
</x-app-layout>
