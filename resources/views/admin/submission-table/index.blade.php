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
                                                $val = $s->decoded_data[$col] ?? '-';
                                                $filesObj = $s->files ?? [];
                                                if (is_string($filesObj)) {
                                                    $filesObj = json_decode($filesObj, true) ?: [];
                                                }
                                                $fileObj = $filesObj[$col] ?? null;

                                                $base64Image = null;

                                        if ($fileObj && isset($fileObj['data']) && Str::startsWith($fileObj['data'], 'data:image')) {
                                            // gambar dari kolom files (form_submissions)
                                            $base64Image = $fileObj['data'];
                                        } elseif (is_array($val) && isset($val['data']) && Str::startsWith($val['data'], 'data:image')) {
                                            // gambar dari kolom data (laporan_selesais)
                                            $base64Image = $val['data'];
                                        } elseif (is_string($val) && Str::startsWith($val, 'data:image')) {
                                            // fallback, kalau langsung base64 string
                                            $base64Image = $val;
                                        }
                                            @endphp

                                            @if($col === 'koordinat_latlng' && !empty($val))
                                                <a href="{{ route('peta.index', ['submission_id' => $s->_id]) }}"
                                                    class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 text-xs shadow-md">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                                    </svg>
                                                    Lihat di Peta
                                                </a>

                                            @elseif($base64Image)
                                                <button type="button"
                                                    onclick="showImageModal('{{ $base64Image }}')"
                                                    class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 text-xs shadow-md">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 10l4.553 2.276A1 1 0 0120 13.382v3.236a1 1 0 01-.447.894L15 20m0-10l-4.553 2.276A1 1 0 0010 13.382v3.236a1 1 0 00.447.894L15 20m0-10V4m0 6l-5-2m0 2l5-2"/>
                                                    </svg>
                                                    Lihat Gambar
                                                </button>

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
             <div id="imageModal"
                class="hidden fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg overflow-hidden shadow-lg max-w-lg animate-fade-in-up">
                    <div class="p-2 flex justify-between items-center bg-gray-100 border-b">
                        <span class="text-sm font-medium text-gray-700">Preview Gambar</span>
                        <button onclick="closeImageModal()" class="text-gray-500 hover:text-gray-800">&times;</button>
                    </div>
                    <img id="modalImage" src="" class="w-full h-auto">
                </div>
            </div>
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
         window.showImageModal = function (src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    window.closeImageModal = function () {
        document.getElementById('modalImage').src = '';
        document.getElementById('imageModal').classList.add('hidden');
    }
    </script>
</x-app-layout>
