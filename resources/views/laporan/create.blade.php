<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Form Pengaduan Laporan
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6">
        @if(session('success'))
            <div class="mb-6 px-4 py-3 rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 px-4 py-3 rounded bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-200 text-sm">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <form method="POST" action="{{ route('laporan.sendOtp') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    @foreach($fields as $field)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ $field['label'] }}
                            </label>

                            @if($field['type'] === 'text' || $field['type'] === 'number')
                                <input type="{{ $field['type'] }}" 
                                       name="{{ $field['name'] }}" 
                                       value="{{ old($field['name']) }}"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                       {{ $field['required'] ? 'required' : '' }}>

                            @elseif($field['type'] === 'textarea')
                                <textarea name="{{ $field['name'] }}" 
                                          rows="4"
                                          class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                          {{ $field['required'] ? 'required' : '' }}>{{ old($field['name']) }}</textarea>

                            @elseif($field['type'] === 'select')
                                <select name="{{ $field['name'] }}" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @foreach($field['options'] as $opt)
                                        <option value="{{ $opt }}" {{ old($field['name']) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                </select>

                            @elseif($field['type'] === 'radio')
                                <div class="flex flex-wrap gap-4">
                                    @foreach($field['options'] as $opt)
                                        <label class="inline-flex items-center text-sm text-gray-700 dark:text-gray-300">
                                            <input type="radio" 
                                                   name="{{ $field['name'] }}" 
                                                   value="{{ $opt }}" 
                                                   {{ old($field['name']) == $opt ? 'checked' : '' }}
                                                   class="text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                                   {{ $field['required'] ? 'required' : '' }}>
                                            <span class="ml-2">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>

                            @elseif($field['type'] === 'file')
                                <input type="file" 
                                       name="{{ $field['name'] }}" 
                                       class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-indigo-50 file:text-indigo-700
                                              hover:file:bg-indigo-100
                                              dark:file:bg-gray-700 dark:file:text-gray-200"
                                       {{ $field['required'] ? 'required' : '' }}>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maksimal 1MB</p>
                            @endif
                        </div>
                    @endforeach

                    <div>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Kirim OTP
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
