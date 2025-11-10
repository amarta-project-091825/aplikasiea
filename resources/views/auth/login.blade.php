<x-guest-layout>
    <!-- Login Header -->
    <div class="mb-8 animate-fade-in-up">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Login Admin</h2>
        <p class="text-gray-600">Masukkan akun Anda untuk mengakses dashboard</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6 animate-fade-in-up" style="animation-delay: 0.1s;">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
            <input id="email" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus 
                   autocomplete="username"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7f1d1d] focus:border-transparent transition-all duration-300 input-focus" 
                   placeholder="admin@example.com">
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
            <div class="relative">
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="current-password"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7f1d1d] focus:border-transparent transition-all duration-300 input-focus" 
                       placeholder="••••••••">
            </div>
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me" 
                   type="checkbox" 
                   name="remember"
                   class="w-4 h-4 text-[#7f1d1d] border-gray-300 rounded focus:ring-[#7f1d1d] focus:ring-2 transition-all duration-300">
            <label for="remember_me" class="ml-2 text-sm text-gray-700 cursor-pointer">Ingat saya</label>
        </div>

        <!-- Login Button -->
        <button type="submit" 
                class="w-full bg-gradient-to-r from-[#7f1d1d] to-[#5a1515] text-white font-semibold py-3 px-6 rounded-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300 btn-hover focus:outline-none focus:ring-2 focus:ring-[#7f1d1d] focus:ring-offset-2">
            <span class="flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                Masuk
            </span>
        </button>
    </form>

    <!-- Footer Text -->
    <div class="mt-8 text-center text-sm text-gray-600 animate-fade-in-up" style="animation-delay: 0.2s;">
        <p>© 2025 Amarta. All rights reserved.</p>
    </div>
</x-guest-layout>
