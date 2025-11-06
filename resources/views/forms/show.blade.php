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

                <form method="POST" action="{{ route('forms.submit', $form->slug) }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    @foreach($form->fields ?? [] as $index => $f)
                        @php
                            $required = !empty($f['required']);
                            $name = $f['name'];
                            $oldVal = old($name);
                        @endphp

                        <div>
                            <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ $f['label'] }} {!! $required ? '<span class="text-red-500">*</span>' : '' !!}
                            </label>

                            {{-- TEXTAREA --}}
                            @if($f['type'] === 'textarea')
                                <textarea id="{{ $name }}" name="{{ $name }}" rows="4"
                                          class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                          placeholder="{{ $f['placeholder'] ?? '' }}" {{ $required ? 'required' : '' }}>{{ $oldVal }}</textarea>

                            {{-- INPUT STANDARD --}}
                            @elseif(in_array($f['type'], ['text','email','tel','number','date']))
                                <input id="{{ $name }}" name="{{ $name }}" type="{{ $f['type'] }}"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                       placeholder="{{ $f['placeholder'] ?? '' }}" value="{{ $oldVal }}" {{ $required ? 'required' : '' }}>

                            {{-- SELECT --}}
                            @elseif($f['type'] === 'select')
                                <select id="{{ $name }}" name="{{ $name }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        {{ $required ? 'required' : '' }}>
                                    <option value="">-- Pilih --</option>
                                    @foreach($f['options'] ?? [] as $opt)
                                        <option value="{{ $opt }}" @selected($oldVal===$opt)>{{ $opt }}</option>
                                    @endforeach
                                </select>

                            {{-- RADIO --}}
                            @elseif($f['type'] === 'radio')
                                <div class="flex flex-wrap gap-4">
                                    @foreach($f['options'] ?? [] as $opt)
                                        <label class="inline-flex items-center text-sm text-gray-700 dark:text-gray-300">
                                            <input type="radio" name="{{ $name }}" value="{{ $opt }}"
                                                   class="text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                                   @checked($oldVal===$opt) {{ $required ? 'required' : '' }}>
                                            <span class="ml-2">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>

                            {{-- CHECKBOX --}}
                            @elseif($f['type'] === 'checkbox')
                                @php $oldArr = (array) old($name, []); @endphp
                                <div class="flex flex-wrap gap-4">
                                    @foreach($f['options'] ?? [] as $opt)
                                        <label class="inline-flex items-center text-sm text-gray-700 dark:text-gray-300">
                                            <input type="checkbox" name="{{ $name }}[]" value="{{ $opt }}"
                                                   class="text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                                   @checked(in_array($opt,$oldArr,true))>
                                            <span class="ml-2">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>

                            {{-- FILE --}}
                            @elseif($f['type'] === 'file')
                                <input id="{{ $name }}" name="{{ $name }}" type="file"
                                       class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-indigo-50 file:text-indigo-700
                                              hover:file:bg-indigo-100
                                              dark:file:bg-gray-700 dark:file:text-gray-200"
                                       {{ $required ? 'required' : '' }}>
                            @endif

                            {{-- MAP DRAWER --}}
                            @if($f['type'] === 'map_drawer')
                                <div class="mt-4">
                                    <div id="map_{{ $index }}" class="h-64 w-full border rounded mb-2"></div>
                                    <input type="hidden" name="{{ $f['name'] }}_latlng" id="input_{{ $index }}" value="{{ old($f['name'].'_latlng') }}">
                                    <small class="text-gray-500">Gambar jalan di peta, koordinat akan otomatis terisi.</small>
                                </div>
                            @endif

                            {{-- Error --}}
                            @error($name)
                                <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach

                    <div>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

    <script>
        @foreach($form->fields ?? [] as $index => $f)
            @if($f['type'] === 'map_drawer')
                const map{{$index}} = L.map('map_{{$index}}').setView([-8.0983, 112.1688], 12);

                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; OpenStreetMap &copy; CARTO',
                    subdomains: 'abcd',
                    maxZoom: 19
                }).addTo(map{{$index}});

                const drawnItems{{$index}} = new L.FeatureGroup();
                map{{$index}}.addLayer(drawnItems{{$index}});

                const drawControl{{$index}} = new L.Control.Draw({
                    draw: { polygon:false, circle:false, rectangle:false, marker:false, circlemarker:false, polyline:true },
                    edit: { featureGroup: drawnItems{{$index}} }
                });
                map{{$index}}.addControl(drawControl{{$index}});

                map{{$index}}.on(L.Draw.Event.CREATED, function(e){
                    const layer = e.layer;
                    drawnItems{{$index}}.clearLayers();
                    drawnItems{{$index}}.addLayer(layer);
                    const latlngs = layer.getLatLngs().map(ll => [ll.lat, ll.lng]);
                    document.getElementById('input_{{$index}}').value = JSON.stringify(latlngs);
                });

                map{{$index}}.on('draw:edited draw:deleted', function(){
                    const layers = drawnItems{{$index}}.getLayers();
                    if(layers.length > 0){
                        const latlngs = layers[0].getLatLngs().map(ll => [ll.lat, ll.lng]);
                        document.getElementById('input_{{$index}}').value = JSON.stringify(latlngs);
                    } else {
                        document.getElementById('input_{{$index}}').value = '';
                    }
                });

                @if(!empty(old($f['name'].'_latlng')))
                    const coords{{$index}} = JSON.parse(@json(old($f['name'].'_latlng')));
                    const polyline{{$index}} = L.polyline(coords{{$index}}).addTo(drawnItems{{$index}});
                    map{{$index}}.fitBounds(polyline{{$index}}.getBounds());
                @endif
            @endif
        @endforeach
    </script>
</x-app-layout>
