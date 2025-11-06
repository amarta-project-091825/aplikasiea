<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Submission Table
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Daftar Submission</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Data dinamis dari form yang telah diisi pengguna.</p>
                </div>

                {{-- Dropdown filter form --}}
                <div>
                    <form method="GET" action="{{ route('admin.submission.table') }}">
                        <select name="form_id" onchange="this.form.submit()"
                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm">
                            @foreach($forms as $form)
                                <option value="{{ $form->_id }}" {{ $currentForm && $currentForm->_id == $form->_id ? 'selected' : '' }}>
                                    {{ $form->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.submission.batchDestroy') }}" onsubmit="return confirm('Yakin hapus semua yang dipilih?')">
                @csrf
                @method('DELETE')

                <div class="p-2 flex justify-end">
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50"
                        id="batchDeleteButton"
                        disabled>
                        Hapus Terpilih
                    </button>
                </div>

                <div class="p-0 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr>
                                <th class="px-4 py-3">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                                </th>
                                @foreach($columns as $col)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ ucfirst($col) }}
                                    </th>
                                @endforeach
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($submissions as $s)
                                <tr>
                                    <td class="px-4 py-4">
                                        <input type="checkbox" name="ids[]" value="{{ $s->_id }}" class="rowCheckbox rounded border-gray-300">
                                    </td>
                                    @foreach($columns as $col)
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                            @php
                                                $val = $s->decoded_data[$col] ?? '-';
                                            @endphp
                                           @if($col === 'koordinat_latlng' && !empty($val))
                                                <a href="{{ route('peta.index', ['submission_id' => $s->_id]) }}"
                                                class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700 text-xs">
                                                    Lihat di Peta
                                                </a>
                                            @elseif(is_array($val))
                                                @php
                                                    $isAssoc = array_keys($val) !== range(0, count($val) - 1);
                                                @endphp

                                                @if($isAssoc)
                                                    <div class="text-xs text-gray-600 dark:text-gray-300">
                                                        @foreach($val as $k => $v)
                                                            <div>{{ $k }}: {{ is_array($v) ? json_encode($v) : $v }}</div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    {{ implode(', ', array_map(fn($v) => is_array($v) ? json_encode($v) : $v, $val)) }}
                                                @endif
                                            @elseif(is_string($val) && Str::startsWith($val, 'data:image'))
                                                <img src="{{ $val }}" class="h-16 w-16 object-cover rounded">
                                            @else
                                                {{ $val }}
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="px-6 py-4 text-right text-sm">
                                        <a href="{{ route('admin.submission.edit',$s->_id) }}"
                                            class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($columns)+2 }}" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Belum ada submission.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>        
                
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 text-sm text-gray-500 dark:text-gray-400">
                {{ $submissions->links() }}
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
