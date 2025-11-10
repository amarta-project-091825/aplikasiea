<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section with Date & Time -->
            <div class="welcome-gradient rounded-xl shadow-lg p-6 mb-8 text-white animate-fade-in-up welcome-card">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-4 md:mb-0">
                        <h1 class="text-2xl md:text-3xl font-bold mb-2">
                            <span class="flex items-center">
                                <svg class="w-8 h-8 mr-3 text-[#ffb800] user-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                                Selamat Datang, {{ Auth::user()->name }}!
                            </span>
                        </h1>
                        <p class="text-gray-200 text-sm md:text-base">
                            {{ Auth::user()->role->name ?? 'Administrator' }} • Dashboard Overview
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl md:text-3xl font-bold text-[#ffb800] clock-display" id="current-time">
                            --:--:--
                        </div>
                        <div class="text-sm md:text-base text-gray-200" id="current-date">
                            Loading...
                        </div>
                        <div class="text-xs text-gray-300 mt-1" id="current-day">
                            Loading...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Users Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 animate-fade-in-up">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="total-users">{{ $stats['total_users'] }}</p>
                            <div class="flex items-center mt-2 text-sm">
                                <span class="text-green-600 dark:text-green-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $stats['admin_users'] + $stats['petugas_users'] + $stats['validator_users'] }} Active
                                </span>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Forms Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 animate-fade-in-up" style="animation-delay: 0.1s;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Forms</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="total-forms">{{ $stats['total_forms'] }}</p>
                            <div class="flex items-center mt-2 text-sm">
                                <span class="text-blue-600 dark:text-blue-400">Available Forms</span>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Submissions Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 animate-fade-in-up" style="animation-delay: 0.2s;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Submissions</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="total-submissions">{{ $stats['total_submissions'] }}</p>
                            <div class="flex items-center mt-2 text-sm">
                                <span class="text-green-600 dark:text-green-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Form Responses
                                </span>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-green-500 to-green-600 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Laporan Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 animate-fade-in-up" style="animation-delay: 0.3s;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Laporan</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="total-laporan">{{ $stats['total_laporan'] }}</p>
                            <div class="flex items-center mt-2 text-sm">
                                <span class="text-amber-600 dark:text-amber-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $stats['laporan_pending'] }} Pending
                                </span>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-amber-500 to-amber-600 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Tables Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Laporan Status Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 animate-fade-in-up" style="animation-delay: 0.4s;">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status Laporan</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Pending</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-lg font-bold text-gray-900 dark:text-white mr-2" id="laporan-pending">{{ $stats['laporan_pending'] }}</span>
                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-yellow-500 h-2 rounded-full transition-all duration-500" style="width: {{ $stats['total_laporan'] > 0 ? ($stats['laporan_pending'] / $stats['total_laporan']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Ditindaklanjuti</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-lg font-bold text-gray-900 dark:text-white mr-2" id="laporan-ditindaklanjuti">{{ $stats['laporan_ditindaklanjuti'] }}</span>
                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" style="width: {{ $stats['total_laporan'] > 0 ? ($stats['laporan_ditindaklanjuti'] / $stats['total_laporan']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Selesai</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-lg font-bold text-gray-900 dark:text-white mr-2" id="laporan-selesai">{{ $stats['laporan_selesai'] }}</span>
                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full transition-all duration-500" style="width: {{ $stats['total_laporan'] > 0 ? ($stats['laporan_selesai'] / $stats['total_laporan']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Ditolak</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-lg font-bold text-gray-900 dark:text-white mr-2" id="laporan-ditolak">{{ $stats['laporan_ditolak'] }}</span>
                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-red-500 h-2 rounded-full transition-all duration-500" style="width: {{ $stats['total_laporan'] > 0 ? ($stats['laporan_ditolak'] / $stats['total_laporan']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Distribution Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 animate-fade-in-up" style="animation-delay: 0.5s;">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Distribusi User</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-indigo-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Admin</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-lg font-bold text-gray-900 dark:text-white mr-2">{{ $stats['admin_users'] }}</span>
                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-indigo-500 h-2 rounded-full transition-all duration-500" style="width: {{ $stats['total_users'] > 0 ? ($stats['admin_users'] / $stats['total_users']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-emerald-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Petugas Data</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-lg font-bold text-gray-900 dark:text-white mr-2">{{ $stats['petugas_users'] }}</span>
                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $stats['total_users'] > 0 ? ($stats['petugas_users'] / $stats['total_users']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-amber-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Validator</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-lg font-bold text-gray-900 dark:text-white mr-2">{{ $stats['validator_users'] }}</span>
                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-amber-500 h-2 rounded-full transition-all duration-500" style="width: {{ $stats['total_users'] > 0 ? ($stats['validator_users'] / $stats['total_users']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities Tables -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Submissions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 animate-fade-in-up" style="animation-delay: 0.6s;">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Submissions</h3>
                        <a href="{{ route('admin.form-submissions.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/40">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Form</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($recent_submissions as $submission)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $submission->form->name ?? 'Unknown' }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $submission->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-3 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No submissions yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Laporan -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 animate-fade-in-up" style="animation-delay: 0.7s;">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Laporan</h3>
                        <a href="{{ route('admin.laporan-validasi.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/40">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tracking Code</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($recent_laporan as $laporan)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $laporan->tracking_code ?? 'N/A' }}</td>
                                        <td class="px-3 py-2">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                {{ $laporan->status->label === 'Pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200' : '' }}
                                                {{ $laporan->status->label === 'Ditindaklanjuti' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200' : '' }}
                                                {{ $laporan->status->label === 'Selesai' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200' : '' }}
                                                {{ $laporan->status->label === 'Ditolak' ? 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200' : '' }}">
                                                {{ $laporan->status->label }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-3 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No laporan yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Auto-refresh Status -->
            <div class="mt-6 text-center">
                <div class="inline-flex items-center px-4 py-2 bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 rounded-full text-sm">
                    <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Auto-refresh: <span id="last-updated">Just now</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Dashboard Script -->
    <script>
        // Real-time clock and date
        function updateDateTime() {
            const now = new Date();
            
            // Update time
            const timeElement = document.getElementById('current-time');
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');
            timeElement.textContent = `${hours}:${minutes}:${seconds}`;
            
            // Update date
            const dateElement = document.getElementById('current-date');
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            const dayName = days[now.getDay()];
            const day = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            
            dateElement.textContent = `${day} ${monthName} ${year}`;
            
            // Update day
            const dayElement = document.getElementById('current-day');
            dayElement.textContent = dayName;
        }
        
        // Update datetime immediately and then every second
        updateDateTime();
        setInterval(updateDateTime, 1000);
        
        // Greeting based on time
        function updateGreeting() {
            const now = new Date();
            const hour = now.getHours();
            const greetingElement = document.querySelector('h1 span');
            
            let greeting = 'Selamat ';
            if (hour >= 3 && hour < 10) {
                greeting += 'Pagi';
            } else if (hour >= 10 && hour < 15) {
                greeting += 'Siang';
            } else if (hour >= 15 && hour < 18) {
                greeting += 'Sore';
            } else {
                greeting += 'Malam';
            }
            
            // Update greeting text
            const userName = greetingElement.textContent.split(', ')[1];
            greetingElement.innerHTML = `
                <svg class="w-8 h-8 mr-3 text-[#ffb800]" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
                ${greeting}, ${userName}
            `;
        }
        
        // Update greeting immediately and every minute
        updateGreeting();
        setInterval(updateGreeting, 60000);
        
        // Auto-refresh dashboard data every 30 seconds
        let refreshInterval;
        
        function updateDashboardData() {
            fetch('/api/dashboard/realtime')
                .then(response => response.json())
                .then(data => {
                    // Update statistics with animation
                    animateValue('total-users', parseInt(document.getElementById('total-users').textContent), data.total_users, 1000);
                    animateValue('total-forms', parseInt(document.getElementById('total-forms').textContent), data.total_forms, 1000);
                    animateValue('total-submissions', parseInt(document.getElementById('total-submissions').textContent), data.total_submissions, 1000);
                    animateValue('total-laporan', parseInt(document.getElementById('total-laporan').textContent), data.total_laporan, 1000);
                    animateValue('laporan-pending', parseInt(document.getElementById('laporan-pending').textContent), data.laporan_pending, 1000);
                    animateValue('laporan-ditindaklanjuti', parseInt(document.getElementById('laporan-ditindaklanjuti').textContent), data.laporan_ditindaklanjuti, 1000);
                    animateValue('laporan-selesai', parseInt(document.getElementById('laporan-selesai').textContent), data.laporan_selesai, 1000);
                    animateValue('laporan-ditolak', parseInt(document.getElementById('laporan-ditolak').textContent), data.laporan_ditolak, 1000);
                    
                    // Update last updated time
                    document.getElementById('last-updated').textContent = 'Just now';
                    
                    // Show notification if data changed
                    showUpdateNotification();
                })
                .catch(error => {
                    console.error('Error updating dashboard:', error);
                });
        }
        
        function animateValue(id, start, end, duration) {
            const element = document.getElementById(id);
            const range = end - start;
            const increment = range / (duration / 16);
            let current = start;
            
            const timer = setInterval(() => {
                current += increment;
                if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                    element.textContent = end;
                    clearInterval(timer);
                } else {
                    element.textContent = Math.round(current);
                }
            }, 16);
        }
        
        function showUpdateNotification() {
            // Create a subtle notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 animate-pulse';
            notification.textContent = 'Data updated';
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 2000);
        }
        
        // Start auto-refresh
        refreshInterval = setInterval(updateDashboardData, 30000); // 30 seconds
        
        // Update "last updated" text every minute
        setInterval(() => {
            const element = document.getElementById('last-updated');
            const currentText = element.textContent;
            if (currentText === 'Just now') {
                element.textContent = '1 minute ago';
            } else {
                const minutes = parseInt(currentText) + 1;
                element.textContent = minutes + ' minutes ago';
            }
        }, 60000);
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });
    </script>
</x-app-layout>
