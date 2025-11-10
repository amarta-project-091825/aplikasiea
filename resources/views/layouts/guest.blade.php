<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin Login</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
            <div class="w-full max-w-6xl mx-4">
                <div class="flex flex-col md:flex-row bg-white rounded-2xl shadow-2xl overflow-hidden">
                    <!-- Left Side - Logo Section -->
                    <div class="md:w-1/2 bg-gradient-to-br from-[#7f1d1d] to-[#5a1515] p-12 flex flex-col items-center justify-center relative overflow-hidden">
                        <!-- Decorative circles -->
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-32 -mt-32"></div>
                        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white opacity-5 rounded-full -ml-24 -mb-24"></div>
                        
                        <!-- Logo -->
                        <div class="relative z-10 animate-float">
                            <img src="{{ asset('images/default-logo-header.png') }}" alt="Logo" class="w-64 h-auto mb-8 drop-shadow-2xl">
                        </div>
                        
                        <!-- Welcome Text -->
                        <div class="relative z-10 text-center text-white">
                            <h1 class="text-4xl font-bold mb-4 animate-fade-in-up">Selamat Datang</h1>
                            <p class="text-lg opacity-90 animate-fade-in-up" style="animation-delay: 0.2s;">Silakan login untuk melanjutkan</p>
                        </div>
                        
                        <!-- Decorative elements -->
                        <div class="absolute bottom-8 left-8 right-8 flex justify-center space-x-2">
                            <div class="w-2 h-2 bg-white rounded-full opacity-50"></div>
                            <div class="w-2 h-2 bg-white rounded-full opacity-75"></div>
                            <div class="w-2 h-2 bg-white rounded-full opacity-100"></div>
                        </div>
                    </div>

                    <!-- Right Side - Login Form -->
                    <div class="md:w-1/2 p-12">
                        <div class="max-w-md mx-auto">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
