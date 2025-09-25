<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">{{ $form->name }}</h2></x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            @if (session('status'))
                <div class="mb-4 text-green-600">{{ session('status') }}</div>
            @endif

            @if($form->description)
                <p class="mb-4 text-gray-600">{{ $form->description }}</p>
            @endif

            <form method="post" action="{{ route('forms.submit', $form->slug) }}" enctype="multipart/form-data">
                @csrf

                @foreach(($form->fields ?? []) as $f)
                    @php
                        $required = !empty($f['required']);
                        $name = $f['name'];
                        $placeholder = $f['placeholder'] ?? '';
                        $oldVal = old($name);
                    @endphp

                    <div class="mb-4">
                        <x-input-label :for="$name" :value="$f['label'] . ($required ? ' *' : '')" />
                        @if($f['type'] === 'textarea')
                            <textarea id="{{ $name }}" name="{{ $name }}" class="mt-1 block w-full border rounded p-2" placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}>{{ $oldVal }}</textarea>

                        @elseif(in_array($f['type'], ['text','email','tel','number','date']))
                            <input id="{{ $name }}" name="{{ $name }}" type="{{ $f['type'] }}" class="mt-1 block w-full border rounded p-2" placeholder="{{ $placeholder }}" value="{{ $oldVal }}" {{ $required ? 'required' : '' }}>

                        @elseif($f['type'] === 'select')
                            <select id="{{ $name }}" name="{{ $name }}" class="mt-1 block w-full border rounded p-2" {{ $required ? 'required' : '' }}>
                                <option value="">-- Pilih --</option>
                                @foreach(($f['options'] ?? []) as $opt)
                                    <option value="{{ $opt }}" @selected($oldVal===$opt)>{{ $opt }}</option>
                                @endforeach
                            </select>

                        @elseif($f['type'] === 'radio')
                            <div class="mt-1">
                                @foreach(($f['options'] ?? []) as $opt)
                                    <label class="inline-flex items-center me-4">
                                        <input type="radio" name="{{ $name }}" value="{{ $opt }}" @checked($oldVal===$opt) {{ $required ? 'required' : '' }}>
                                        <span class="ms-2">{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>

                        @elseif($f['type'] === 'checkbox')
                            <div class="mt-1">
                                @php $oldArr = (array) old($name, []); @endphp
                                @foreach(($f['options'] ?? []) as $opt)
                                    <label class="inline-flex items-center me-4">
                                        <input type="checkbox" name="{{ $name }}[]" value="{{ $opt }}" @checked(in_array($opt,$oldArr,true))>
                                        <span class="ms-2">{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>

                        @elseif($f['type'] === 'file')
                            <input id="{{ $name }}" name="{{ $name }}" type="file" class="mt-1 block w-full border rounded p-2" {{ $required ? 'required' : '' }}>
                            @if(!empty($f['mimes']))<small class="text-gray-500">Format: {{ implode(', ', $f['mimes']) }}</small>@endif
                            @if(!empty($f['max']))<small class="text-gray-500 ms-3">Maks: {{ (int)$f['max'] }} KB</small>@endif
                        @endif

                        @error($name)
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach

                <x-primary-button>Kirim</x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>
