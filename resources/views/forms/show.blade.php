<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $form->name }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                @if (session('status'))
                    <div class="mb-6 px-4 py-3 rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                @if($form->description)
                    <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">{{ $form->description }}</p>
                @endif

                <form method="post" action="{{ route('forms.submit', $form->slug) }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    @foreach(($form->fields ?? []) as $f)
                        @php
                            $required = !empty($f['required']);
                            $name = $f['name'];
                            $placeholder = $f['placeholder'] ?? '';
                            $oldVal = old($name);
                        @endphp

                        <div>
                            <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ $f['label'] }} {!! $required ? '<span class="text-red-500">*</span>' : '' !!}
                            </label>

                            {{-- Textarea --}}
                            @if($f['type'] === 'textarea')
                                <textarea id="{{ $name }}" name="{{ $name }}" rows="4"
                                          class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                          placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}>{{ $oldVal }}</textarea>

                            {{-- Input teks, email, tel, number, date --}}
                            @elseif(in_array($f['type'], ['text','email','tel','number','date']))
                                <input id="{{ $name }}" name="{{ $name }}" type="{{ $f['type'] }}"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                       placeholder="{{ $placeholder }}" value="{{ $oldVal }}" {{ $required ? 'required' : '' }}>

                            {{-- Select --}}
                            @elseif($f['type'] === 'select')
                                <select id="{{ $name }}" name="{{ $name }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        {{ $required ? 'required' : '' }}>
                                    <option value="">-- Pilih --</option>
                                    @foreach(($f['options'] ?? []) as $opt)
                                        <option value="{{ $opt }}" @selected($oldVal===$opt)>{{ $opt }}</option>
                                    @endforeach
                                </select>

                            {{-- Radio --}}
                            @elseif($f['type'] === 'radio')
                                <div class="flex flex-wrap gap-4">
                                    @foreach(($f['options'] ?? []) as $opt)
                                        <label class="inline-flex items-center text-sm text-gray-700 dark:text-gray-300">
                                            <input type="radio" name="{{ $name }}" value="{{ $opt }}"
                                                   class="text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                                   @checked($oldVal===$opt) {{ $required ? 'required' : '' }}>
                                            <span class="ml-2">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>

                            {{-- Checkbox --}}
                            @elseif($f['type'] === 'checkbox')
                                @php $oldArr = (array) old($name, []); @endphp
                                <div class="flex flex-wrap gap-4">
                                    @foreach(($f['options'] ?? []) as $opt)
                                        <label class="inline-flex items-center text-sm text-gray-700 dark:text-gray-300">
                                            <input type="checkbox" name="{{ $name }}[]" value="{{ $opt }}"
                                                   class="text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                                   @checked(in_array($opt,$oldArr,true))>
                                            <span class="ml-2">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>

                            {{-- File --}}
                            @elseif($f['type'] === 'file')
                                <input id="{{ $name }}" name="{{ $name }}" type="file"
                                       class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-indigo-50 file:text-indigo-700
                                              hover:file:bg-indigo-100
                                              dark:file:bg-gray-700 dark:file:text-gray-200"
                                       {{ $required ? 'required' : '' }}>
                                @if(!empty($f['mimes']))
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: {{ implode(', ', $f['mimes']) }}</p>
                                @endif
                                @if(!empty($f['max']))
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maks: {{ (int)$f['max'] }} KB</p>
                                @endif
                            @endif

                            {{-- Error --}}
                            @error($name)
                                <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach

                    {{-- Submit --}}
                    <div>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
