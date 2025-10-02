<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Form Pengaduan Laporan</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6">
        @if(session('success'))
            <div class="p-4 mb-4 text-green-700 bg-green-100 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('laporan.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            @foreach($fields as $field)
                <div>
                    <label class="block text-sm font-medium mb-1">{{ $field['label'] }}</label>

                    @if($field['type'] === 'text' || $field['type'] === 'number')
                        <input type="{{ $field['type'] }}" 
                               name="{{ $field['name'] }}" 
                               class="w-full border rounded p-2"
                               {{ $field['required'] ? 'required' : '' }}>

                    @elseif($field['type'] === 'textarea')
                        <textarea name="{{ $field['name'] }}" 
                                  class="w-full border rounded p-2"
                                  {{ $field['required'] ? 'required' : '' }}></textarea>

                    @elseif($field['type'] === 'select')
                        <select name="{{ $field['name'] }}" class="w-full border rounded p-2">
                            @foreach($field['options'] as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>

                    @elseif($field['type'] === 'radio')
                        @foreach($field['options'] as $opt)
                            <label class="inline-flex items-center mr-4">
                                <input type="radio" name="{{ $field['name'] }}" value="{{ $opt }}" {{ $field['required'] ? 'required' : '' }}>
                                <span class="ml-1">{{ $opt }}</span>
                            </label>
                        @endforeach

                    @elseif($field['type'] === 'file')
                        <input type="file" 
                               name="{{ $field['name'] }}" 
                               class="w-full border rounded p-2"
                               {{ $field['required'] ? 'required' : '' }}>
                        <small class="text-gray-500">Maksimal 1MB</small>
                    @endif
                </div>
            @endforeach

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Kirim</button>
        </form>
    </div>
</x-app-layout>
