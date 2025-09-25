<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Edit Submission</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            <form method="post" action="{{ route('admin.submission.update', $submission->_id) }}" enctype="multipart/form-data">
                @csrf
                @method('put')

                @foreach($form->fields as $f)
                    @php
                        $name = $f['name'];
                        $type = $f['type'];
                        $value = $submission->data[$name] ?? null;
                    @endphp

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $f['label'] }}
                        </label>

                        {{-- Text-based --}}
                        @if(in_array($type, ['text','email','tel','number','date']))
                            <input 
                                type="{{ $type }}" 
                                name="{{ $name }}" 
                                value="{{ is_array($value) ? implode(', ', $value) : $value }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 
                                       dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 
                                       focus:ring-indigo-500 sm:text-sm">
                        @endif

                        {{-- Textarea --}}
                        @if($type === 'textarea')
                            <textarea name="{{ $name }}" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 
                                       dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 
                                       focus:ring-indigo-500 sm:text-sm">{{ is_array($value) ? implode(', ', $value) : $value }}</textarea>
                        @endif

                        {{-- Select --}}
                        @if($type === 'select')
                            <select name="{{ $name }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 
                                       dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 
                                       focus:ring-indigo-500 sm:text-sm">
                                @foreach($f['options'] ?? [] as $opt)
                                    <option value="{{ $opt }}" @selected($value == $opt)>{{ $opt }}</option>
                                @endforeach
                            </select>
                        @endif

                        {{-- Radio --}}
                        @if($type === 'radio')
                            <div class="mt-2 space-y-1">
                                @foreach($f['options'] ?? [] as $opt)
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="{{ $name }}" value="{{ $opt }}" @checked($value == $opt)>
                                        <span>{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        {{-- Checkbox --}}
                        @if($type === 'checkbox')
                            <div class="mt-2 space-y-1">
                                @php $valArr = is_array($value) ? $value : []; @endphp
                                @foreach($f['options'] ?? [] as $opt)
                                    <label class="inline-flex items-center gap-2">
                                        <input type="checkbox" name="{{ $name }}[]" value="{{ $opt }}" @checked(in_array($opt, $valArr))>
                                        <span>{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        {{-- File --}}
                        @if($type === 'file')
                            @if(!empty($submission->files[$name]))
                                <div class="mt-1">
                                    <a href="{{ Storage::url($submission->files[$name]) }}" 
                                       target="_blank" 
                                       class="text-indigo-600 underline">
                                        Lihat file lama
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="{{ $name }}" class="mt-2 block w-full text-sm text-gray-500">
                        @endif
                    </div>
                @endforeach

                <div class="mt-6">
                    <x-primary-button>Simpan Perubahan</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
