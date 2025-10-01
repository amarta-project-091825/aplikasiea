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

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-0 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr>
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
                                    @foreach($columns as $col)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        @php
                                            $file = $s->files[$col] ?? null;
                                            $val  = $s->data[$col] ?? '-';
                                        @endphp

                                        {{-- Jika kolom ini adalah file --}}
                                        @if($file)
                                            {{-- Multiple files --}}
                                            @if(isset($file[0]) && is_array($file[0]))
                                                @foreach($file as $f)
                                                    @if(Str::startsWith($f['mime'], 'image/'))
                                                        <img src="{{ $f['data'] }}" 
                                                            alt="{{ $f['name'] }}" 
                                                            class="h-16 w-auto rounded shadow mb-1">
                                                    @else
                                                        <a href="{{ $f['data'] }}" 
                                                        download="{{ $f['name'] }}" 
                                                        class="text-indigo-600 underline block">
                                                            {{ $f['name'] }}
                                                        </a>
                                                    @endif
                                                @endforeach
                                            {{-- Single file --}}
                                            @else
                                                @if(Str::startsWith($file['mime'], 'image/'))
                                                    <img src="{{ $file['data'] }}" 
                                                        alt="{{ $file['name'] }}" 
                                                        class="h-16 w-auto rounded shadow">
                                                @else
                                                    <a href="{{ $file['data'] }}" 
                                                    download="{{ $file['name'] }}" 
                                                    class="text-indigo-600 underline">
                                                        {{ $file['name'] }}
                                                    </a>
                                                @endif
                                            @endif
                                        {{-- Jika bukan file --}}
                                        @else
                                            @if(is_array($val))
                                                {{ implode(', ', $val) }}
                                            @else
                                                {{ $val }}
                                            @endif
                                        @endif
                                    </td>
                                    @endforeach
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <div class="inline-flex gap-2">
                                            <a href="{{ route('admin.submission.edit',$s->_id) }}"
                                            class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.submission.destroy',$s->_id) }}" method="post" class="inline">
                                                @csrf @method('delete')
                                                <button type="submit" class="px-3 py-1 rounded border border-red-300 text-red-700 hover:bg-red-50" onclick="return confirm('Hapus submission ini?')">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($columns)+1 }}" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Belum ada submission.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 text-sm text-gray-500 dark:text-gray-400">
                    {{ $submissions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
