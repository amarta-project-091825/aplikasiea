<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Verifikasi OTP
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">

                {{-- Pesan success/error --}}
                @if(session('success'))
                    <p class="text-green-600 dark:text-green-400 mb-4">{{ session('success') }}</p>
                @endif

                @if(session('error'))
                    <p class="text-red-600 dark:text-red-400 mb-4">{{ session('error') }}</p>
                @endif

                <form action="{{ route('laporan.verifyOtp') }}" method="POST" id="otpForm" class="space-y-4">
                    @csrf
                    <input type="hidden" name="otp_id" value="{{ $otp_id }}">

                    {{-- Slot OTP 6 digit --}}
                    <div class="flex justify-between space-x-2">
                        @for($i = 0; $i < 6; $i++)
                            <input type="text" name="otp[]" maxlength="1" pattern="\d" required
                                class="w-12 h-12 text-center text-xl border border-gray-300 dark:border-gray-600 rounded focus:ring focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200 otp-input">
                        @endfor
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded">
                        Verifikasi
                    </button>
                </form>

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
