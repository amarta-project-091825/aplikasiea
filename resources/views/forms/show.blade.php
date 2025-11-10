<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
            <svg class="w-6 h-6 mr-2 text-[#ffb800]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            {{ $form->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Form Header -->
            <div class="welcome-gradient rounded-xl shadow-lg p-6 mb-8 text-white animate-fade-in-up">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h1 class="text-2xl md:text-3xl font-bold mb-2">{{ $form->name }}</h1>
                        @if($form->description)
                            <p class="text-gray-200 text-sm md:text-base">{{ $form->description }}</p>
                        @endif
                    </div>
                    <div class="bg-white/20 p-4 rounded-lg backdrop-blur-sm">
                        <svg class="w-12 h-12 text-[#ffb800]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('status'))
                <div class="mb-6 px-6 py-4 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 dark:from-green-900/20 dark:to-emerald-900/20 dark:border-green-800 dark:text-green-200 animate-fade-in-up">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('status') }}
                    </div>
                </div>
            @endif

            <!-- Form Container -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="p-8">
                    <form method="POST" action="{{ route('forms.submit', $form->slug) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        @foreach($form->fields ?? [] as $index => $f)
                            @php
                                $required = !empty($f['required']);
                                $name = $f['name'];
                                $oldVal = old($name);
                            @endphp

                            <div class="form-group animate-fade-in-up" style="animation-delay: {{ ($index + 2) * 0.1 }}s;">
                                <label for="{{ $name }}" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-[#7f1d1d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    {{ $f['label'] }} 
                                    @if($required)
                                        <span class="ml-1 text-red-500">*</span>
                                    @endif
                                </label>

                                {{-- TEXTAREA --}}
                                @if($f['type'] === 'textarea')
                                    <textarea id="{{ $name }}" name="{{ $name }}" rows="4"
                                              class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:border-[#7f1d1d] focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300 resize-none input-focus"
                                              placeholder="{{ $f['placeholder'] ?? 'Ketik di sini...' }}" {{ $required ? 'required' : '' }}>{{ $oldVal }}</textarea>

                                {{-- INPUT STANDARD --}}
                                @elseif(in_array($f['type'], ['text','email','tel','number','date']))
                                    <input id="{{ $name }}" name="{{ $name }}" type="{{ $f['type'] }}"
                                           class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:border-[#7f1d1d] focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300 input-focus"
                                           placeholder="{{ $f['placeholder'] ?? 'Masukkan ' . strtolower($f['label']) }}" value="{{ $oldVal }}" {{ $required ? 'required' : '' }}>

                                {{-- SELECT --}}
                                @elseif($f['type'] === 'select')
                                    <select id="{{ $name }}" name="{{ $name }}"
                                            class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:border-[#7f1d1d] focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300 input-focus"
                                            {{ $required ? 'required' : '' }}>
                                        <option value="">-- Pilih {{ $f['label'] }} --</option>
                                        @foreach($f['options'] ?? [] as $opt)
                                            <option value="{{ $opt }}" @selected($oldVal===$opt)>{{ $opt }}</option>
                                        @endforeach
                                    </select>

                                {{-- RADIO --}}
                                @elseif($f['type'] === 'radio')
                                    <div class="space-y-3 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                        @foreach($f['options'] ?? [] as $opt)
                                            <label class="flex items-center p-3 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-500 cursor-pointer hover:border-[#7f1d1d] transition-all duration-300">
                                                <input type="radio" name="{{ $name }}" value="{{ $opt }}"
                                                       class="w-4 h-4 text-[#7f1d1d] focus:ring-[#7f1d1d]/20 border-gray-300 dark:border-gray-500"
                                                       @checked($oldVal===$opt) {{ $required ? 'required' : '' }}>
                                                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-200">{{ $opt }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                {{-- CHECKBOX --}}
                                @elseif($f['type'] === 'checkbox')
                                    @php $oldArr = (array) old($name, []); @endphp
                                    <div class="space-y-3 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                        @foreach($f['options'] ?? [] as $opt)
                                            <label class="flex items-center p-3 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-500 cursor-pointer hover:border-[#7f1d1d] transition-all duration-300">
                                                <input type="checkbox" name="{{ $name }}[]" value="{{ $opt }}"
                                                       class="w-4 h-4 text-[#7f1d1d] focus:ring-[#7f1d1d]/20 border-gray-300 dark:border-gray-500 rounded"
                                                       @checked(in_array($opt,$oldArr,true))>
                                                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-200">{{ $opt }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                {{-- FILE --}}
                                @elseif($f['type'] === 'file')
                                    <div class="relative">
                                        <input id="{{ $name }}" name="{{ $name }}" type="file"
                                               class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4
                                                      file:rounded-lg file:border-0
                                                      file:text-sm file:font-semibold
                                                      file:bg-gradient-to-r file:from-[#7f1d1d] file:to-[#5a1515] file:text-white
                                                      hover:file:from-[#991b1b] hover:file:to-[#7f1d1d]
                                                      dark:file:opacity-90 transition-all duration-300"
                                               {{ $required ? 'required' : '' }}>
                                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Format yang didukung: PDF, DOC, DOCX, JPG, PNG (Max: 5MB)
                                        </div>
                                    </div>
                                @endif

                                {{-- MAP DRAWER --}}
                                @if($f['type'] === 'map_drawer')
                                    <div class="mt-4">
                                        <div class="flex gap-2 mb-3">
                                            <button type="button" 
                                                    class="px-4 py-2 bg-gradient-to-r from-[#7f1d1d] to-[#5a1515] text-white rounded-lg hover:from-[#991b1b] hover:to-[#7f1d1d] transition-all duration-300 transform hover:scale-105 text-sm font-medium btn-hover" 
                                                    onclick="setMode{{$index}}('road')">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                                </svg>
                                                Gambar Jalan
                                            </button>
                                            <button type="button" 
                                                    class="px-4 py-2 bg-gradient-to-r from-[#ffb800] to-[#f59e0b] text-white rounded-lg hover:from-[#f59e0b] hover:to-[#d97706] transition-all duration-300 transform hover:scale-105 text-sm font-medium btn-hover" 
                                                    onclick="setMode{{$index}}('bridge')">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                                Tandai Jembatan
                                            </button>
                                        </div>

                                        <div id="map_{{ $index }}" class="h-64 w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg mb-3 shadow-inner"></div>
                                        <input type="hidden" name="{{ $f['name'] }}_latlng" id="input_{{ $index }}" value="{{ old($f['name'].'_latlng') }}">
                                        <div class="text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Pilih mode, lalu gambar di peta untuk menandai lokasi
                                        </div>
                                    </div>
                                @endif

                                {{-- Error Message --}}
                                @error($name)
                                    <div class="mt-2 flex items-center text-red-600 dark:text-red-400 text-sm animate-fade-in-up">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        @endforeach

                        <!-- Submit Button -->
                        <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-600">
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#7f1d1d] to-[#5a1515] text-white font-semibold rounded-lg hover:from-[#991b1b] hover:to-[#7f1d1d] focus:outline-none focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300 transform hover:scale-105 btn-hover">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Kirim Formulir
                            </button>
                        </div>
                    </form>
                </div>
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
            let mode{{$index}} = 'road';

            const map{{$index}} = L.map('map_{{$index}}').setView([-8.0983, 112.1688], 12);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap &copy; CARTO',
                subdomains: 'abcd',
                maxZoom: 19
            }).addTo(map{{$index}});

            const drawnItems{{$index}} = new L.FeatureGroup();
            map{{$index}}.addLayer(drawnItems{{$index}});

            const drawControl{{$index}} = new L.Control.Draw({
                draw: {
                    polygon:false,
                    circle:false,
                    rectangle:false,
                    circlemarker:false,
                    polyline:true,
                    marker:true
                },
                edit: { featureGroup: drawnItems{{$index}} }
            });
            map{{$index}}.addControl(drawControl{{$index}});

            function setMode{{$index}}(m) {
                mode{{$index}} = m;
                drawnItems{{$index}}.clearLayers();
                document.getElementById('input_{{$index}}').value = '';
                
                // Show custom notification instead of alert
                showNotification('Mode diubah ke: ' + (m === 'road' ? 'Gambar Jalan' : 'Tandai Jembatan'), 'info');
            }

            map{{$index}}.on(L.Draw.Event.CREATED, function(e){
                const layer = e.layer;
                drawnItems{{$index}}.clearLayers();

                // Jika mode jalan → polyline
                if(mode{{$index}} === 'road' && layer instanceof L.Polyline) {
                    drawnItems{{$index}}.addLayer(layer);
                    const latlngs = layer.getLatLngs().map(ll => [ll.lat, ll.lng]);
                    document.getElementById('input_{{$index}}').value = JSON.stringify({
                        type: 'road',
                        coords: latlngs
                    });
                }

                // Jika mode jembatan → marker
                if(mode{{$index}} === 'bridge' && layer instanceof L.Marker) {
                    drawnItems{{$index}}.addLayer(layer);
                    const p = layer.getLatLng();
                    document.getElementById('input_{{$index}}').value = JSON.stringify({
                        type: 'bridge',
                        coord: [p.lat, p.lng]
                    });
                }
            });

            map{{$index}}.on('draw:edited draw:deleted', function(){
                const layers = drawnItems{{$index}}.getLayers();
                if(layers.length > 0){
                    const layer = layers[0];

                    if(layer instanceof L.Polyline){
                        const latlngs = layer.getLatLngs().map(ll => [ll.lat, ll.lng]);
                        document.getElementById('input_{{$index}}').value = JSON.stringify({
                            type: 'road',
                            coords: latlngs
                        });
                    }

                    if(layer instanceof L.Marker){
                        const p = layer.getLatLng();
                        document.getElementById('input_{{$index}}').value = JSON.stringify({
                            type: 'bridge',
                            coord: [p.lat, p.lng]
                        });
                    }
                } else {
                    document.getElementById('input_{{$index}}').value = '';
                }
            });

            @if(!empty(old($f['name'].'_latlng')))
                const saved{{$index}} = JSON.parse(@json(old($f['name'].'_latlng')));
                if(saved{{$index}}.type === 'road'){
                    const poly = L.polyline(saved{{$index}}.coords).addTo(drawnItems{{$index}});
                    map{{$index}}.fitBounds(poly.getBounds());
                } else if(saved{{$index}}.type === 'bridge'){
                    const marker = L.marker(saved{{$index}}.coord).addTo(drawnItems{{$index}});
                    map{{$index}}.setView(saved{{$index}}.coord, 16);
                }
            @endif
        @endif
    @endforeach

    // Custom notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        const bgColor = type === 'info' ? 'from-blue-500 to-blue-600' : 'from-green-500 to-green-600';
        
        notification.className = `fixed top-4 right-4 bg-gradient-to-r ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in-up flex items-center`;
        notification.innerHTML = `
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            ${message}
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
</script>

</x-app-layout>
