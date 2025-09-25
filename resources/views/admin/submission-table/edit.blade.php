<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Submission
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <form method="post" action="{{ route('admin.submission.update', $submission->_id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('put')

                    @foreach($allFields as $fieldName)
                        @php
                            $value = $submission->data[$fieldName] ?? null;
                            $field = collect($form->fields)->firstWhere('name', $fieldName);
                            $type = $field['type'] ?? 'text';
                            $options = $field['options'] ?? [];
                        @endphp

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ ucfirst(str_replace('_', ' ', $fieldName)) }}
                            </label>

                            {{-- TEXTAREA --}}
                            @if($type === 'textarea')
                                <textarea name="{{ $fieldName }}" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ is_array($value) ? implode(', ', $value) : $value }}</textarea>

                            {{-- SELECT --}}
                            @elseif($type === 'select')
                                <select name="{{ $fieldName }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    <option value="">-- pilih --</option>
                                    @foreach($options as $opt)
                                        <option value="{{ $opt }}" {{ $opt == $value ? 'selected' : '' }}>
                                            {{ $opt }}
                                        </option>
                                    @endforeach
                                </select>

                            {{-- RADIO --}}
                            @elseif($type === 'radio')
                                @foreach($options as $opt)
                                    <label class="inline-flex items-center mr-3">
                                        <input type="radio" name="{{ $fieldName }}" value="{{ $opt }}"
                                               class="rounded border-gray-300 text-indigo-600"
                                               {{ $opt == $value ? 'checked' : '' }}>
                                        <span class="ml-1">{{ $opt }}</span>
                                    </label>
                                @endforeach

                            {{-- CHECKBOX --}}
                            @elseif($type === 'checkbox')
                                @foreach($options as $opt)
                                    <label class="inline-flex items-center mr-3">
                                        <input type="checkbox" name="{{ $fieldName }}[]"
                                               value="{{ $opt }}"
                                               class="rounded border-gray-300 text-indigo-600"
                                               {{ in_array($opt, (array) $value) ? 'checked' : '' }}>
                                        <span class="ml-1">{{ $opt }}</span>
                                    </label>
                                @endforeach

                            {{-- FILE --}}
                            @elseif($type === 'file')
                                @if(!empty($submission->files[$fieldName]))
                                    <div class="mb-2">
                                        <a href="{{ Storage::url($submission->files[$fieldName]) }}" target="_blank" class="text-indigo-600 underline">
                                            Lihat file lama
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="{{ $fieldName }}"
                                       class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-200">

                            {{-- DATE / EMAIL / TEL / NUMBER / TEXT --}}
                            @else
                                <input type="{{ $type }}" name="{{ $fieldName }}"
                                       value="{{ is_array($value) ? implode(', ', $value) : $value }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            @endif
                        </div>
                    @endforeach

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
