<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
            <svg class="w-6 h-6 mr-2 text-[#ffb800]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Submission Table
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="welcome-gradient rounded-xl shadow-lg p-6 mb-8 text-white animate-fade-in-up">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="mb-4 md:mb-0 flex-1">
                        <h1 class="text-2xl md:text-3xl font-bold mb-2 flex items-center">
                            <svg class="w-8 h-8 mr-3 text-[#ffb800]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Daftar Submission
                        </h1>
                        <p class="text-gray-200 text-sm md:text-base">
                            Data dinamis dari form yang telah diisi pengguna
                        </p>
                    </div>

                    {{-- Dropdown filter form --}}
                    <div class="w-full md:w-auto">
                        <form method="GET" action="{{ route('admin.submission.table') }}">
                            <label class="block text-sm font-medium mb-2">Filter Form:</label>
                            <select name="form_id" onchange="this.form.submit()"
                                class="w-full md:w-64 px-4 py-3 border-2 border-white/20 bg-white/10 backdrop-blur-sm text-white rounded-lg shadow-sm focus:border-[#ffb800] focus:ring-2 focus:ring-[#ffb800]/20 transition-all duration-300">
                                @foreach($forms as $form)
                                    <option value="{{ $form->_id }}" {{ $currentForm && $currentForm->_id == $form->_id ? 'selected' : '' }}
                                            class="bg-gray-800 text-white">
                                        {{ $form->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden animate-fade-in-up" style="animation-delay: 0.2s;">
                <form method="POST" action="{{ route('admin.submission.batchDestroy') }}" onsubmit="return confirm('Yakin hapus semua yang dipilih?')">
                    @csrf
                    @method('DELETE')

                    <div class="p-4 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-200 dark:border-gray-700 flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold rounded-lg hover:from-red-700 hover:to-red-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 transform hover:scale-105 shadow-lg"
                            id="batchDeleteButton"
                            disabled>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Terpilih
                        </button>
                    </div>

                    <div class="p-0 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900/60 dark:to-gray-900/40">
                                <tr>
                                    <th class="px-4 py-4">
                                        <input type="checkbox" id="selectAll" class="w-5 h-5 text-[#7f1d1d] focus:ring-[#7f1d1d]/20 border-gray-300 dark:border-gray-600 rounded">
                                    </th>
                                    @foreach($columns as $col)
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-[#7f1d1d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                {{ ucfirst(str_replace('_', ' ', $col)) }}
                                            </div>
                                        </th>
                                    @endforeach
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($submissions as $index => $s)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                    <td class="px-4 py-4">
                                        <input type="checkbox" name="ids[]" value="{{ $s->_id }}" class="rowCheckbox w-5 h-5 text-[#7f1d1d] focus:ring-[#7f1d1d]/20 border-gray-300 dark:border-gray-600 rounded">
                                    </td>
                                    @foreach($columns as $col)
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        @php
                                            // safety: decoded_data sudah disiapkan di controller, tapi files kadang string JSON
                                            $val = $s->decoded_data[$col] ?? '-';
                                            $filesObj = $s->files ?? [];
                                            if (is_string($filesObj)) {
                                                $filesObj = json_decode($filesObj, true) ?: [];
                                            }
                                            $fileObj = $filesObj[$col] ?? null;
                                        @endphp

                                        {{-- Tombol lihat peta --}}
                                        @if($col === 'koordinat_latlng' && !empty($val))
                                            <a href="{{ route('peta.index', ['submission_id' => $s->_id]) }}"
                                            class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 text-xs shadow-md">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                                </svg>
                                                Lihat di Peta
                                            </a>

                                        {{-- Jika nilai adalah array (biasa / asosiatif) --}}
                                        @elseif(is_array($val))
                                            @php
                                                $isAssoc = array_keys($val) !== range(0, count($val) - 1);
                                            @endphp

                                            {{-- Kalau ada file terkait (contoh: foto_jalan di files) tampilkan preview --}}
                                            @if($fileObj && isset($fileObj['data']) && Str::startsWith($fileObj['data'], 'data:image'))
                                                <div class="mb-2">
                                                    <img src="{{ $fileObj['data'] }}" class="h-20 w-20 object-cover rounded-lg shadow-md border-2 border-gray-200 dark:border-gray-600">
                                                </div>
                                            @endif

                                            @if($isAssoc)
                                                <div class="text-xs text-gray-600 dark:text-gray-300">
                                                    @foreach($val as $k => $v)
                                                        <div>{{ $k }}: {{ is_array($v) ? json_encode($v) : $v }}</div>
                                                    @endforeach
                                                </div>
                                            @else
                                                {{ implode(', ', array_map(fn($v) => is_array($v) ? json_encode($v) : $v, $val)) }}
                                            @endif

                                        {{-- Kalau string image langsung di data --}}
                                        @elseif(is_string($val) && Str::startsWith($val, 'data:image'))
                                            <img src="{{ $val }}" class="h-20 w-20 object-cover rounded-lg shadow-md border-2 border-gray-200 dark:border-gray-600">

                                        {{-- Kalau string biasa tampilkan teks (atau nama file) --}}
                                        @else
                                            {{ is_array($val) ? json_encode($val) : $val }}
                                        @endif
                                    </td>

                                    @endforeach
                                    <td class="px-6 py-4 text-right text-sm">
                                        <a href="{{ route('admin.submission.edit',$s->_id) }}"
                                            class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg text-blue-700 hover:from-blue-100 hover:to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 dark:border-blue-800 dark:text-blue-300 transition-all duration-300 transform hover:scale-105">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($columns)+2 }}" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-gray-100 dark:bg-gray-700 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Belum Ada Submission</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data submission untuk form ini</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($submissions->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/20">
                        {{ $submissions->links() }}
                    </div>
                @endif
            </form>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.rowCheckbox');
            const deleteBtn = document.getElementById('batchDeleteButton');

            selectAll.addEventListener('change', () => {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
                deleteBtn.disabled = !selectAll.checked;
            });

            checkboxes.forEach(cb => {
                cb.addEventListener('change', () => {
                    const anyChecked = Array.from(checkboxes).some(c => c.checked);
                    deleteBtn.disabled = !anyChecked;
                    selectAll.checked = Array.from(checkboxes).every(c => c.checked);
                });
            });
        });
    </script>
</x-app-layout>
