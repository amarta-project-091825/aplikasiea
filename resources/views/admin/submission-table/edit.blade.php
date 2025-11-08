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

                    @php
                        // Field yang nilainya array kompleks / tidak bisa diinput langsung
                        $skipFields = ['koordinat_latlng']; 
                    @endphp

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
                                @php
                                    $file = $submission->files[$fieldName] ?? null;
                                @endphp

                                @if($file)
                                    <div class="mb-2">
                                        @if(Str::startsWith($file['mime'], 'image/'))
                                            <img src="{{ $file['data'] }}" 
                                                alt="{{ $file['name'] }}" 
                                                class="h-32 w-auto rounded mb-2 border">
                                        @else
                                            <a href="{{ $file['data'] }}" download="{{ $file['name'] }}" 
                                            class="text-indigo-600 underline">
                                                Lihat file lama ({{ $file['name'] }})
                                            </a>
                                        @endif
                                    </div>
                                @endif

                                <input type="file" name="{{ $fieldName }}" 
                                    class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-200">

                            {{-- DATE / EMAIL / TEL / NUMBER / TEXT --}}
                            @else
                                @if(!in_array($fieldName, $skipFields))
                                    <input type="{{ $type }}" name="{{ $fieldName }}"
                                           value="{{ is_array($value) ? implode(', ', $value) : $value }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                @else
                                    {{-- Field kompleks seperti array/map tidak diinput biasa --}}
                                    <input type="hidden" name="{{ $fieldName }}" value="{{ json_encode($value) }}">
                                @endif
                            @endif
                        </div>
                        
                    @endforeach
                       @php
                        $coords = $submission->data['koordinat_latlng'] ?? [];
                        $mapData = [
                            'type' => is_array($coords[0] ?? null) ? 'road' : 'bridge',
                            'coords' => is_array($coords[0] ?? null) ? $coords : [],
                            'coord' => is_array($coords[0] ?? null) ? null : $coords
                        ];
                        @endphp
                            <div id="map" style="height: 400px;" class="mb-4 rounded-md border"></div>
                            <input type="hidden" name="koordinat_latlng" id="koordinat_latlng"
                            value="{{ json_encode($submission->data['koordinat_latlng'] ?? null, JSON_HEX_APOS | JSON_HEX_QUOT) }}">
                           <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log("Memulai map…");
    var map = L.map('map').setView([-8.0983, 112.1688], 13);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap &copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    var drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    var drawControl = new L.Control.Draw({
        edit: { featureGroup: drawnItems },
        draw: {
            polygon: false,
            polyline: true,
            rectangle: false,
            circle: false,
            marker: true
        }
    });
    map.addControl(drawControl);

    // Load data dari submission
    let existingData = document.getElementById('koordinat_latlng').value;
    console.log("Data raw dari hidden input:", existingData);

    if (existingData) {
        try {
            // cek dulu apakah ini string JSON ganda
            if (typeof existingData === 'string') {
                existingData = JSON.parse(existingData);
                console.log("Data setelah parse:", existingData);
            }

            if (!existingData || !existingData.type) {
                console.warn("Data koordinat tidak valid:", existingData);
            } else if (existingData.type === 'road') {
                console.log("Membuat polyline jalan…");
                const poly = L.polyline(existingData.coords, { color: 'blue' }).addTo(drawnItems);
                map.fitBounds(poly.getBounds());
            } else if (existingData.type === 'bridge') {
                console.log("Membuat marker jembatan…");
                const marker = L.marker(existingData.coord).addTo(drawnItems);
                map.setView(existingData.coord, 16);
            } else {
                console.warn("Tipe data tidak dikenali:", existingData.type);
            }
        } catch (e) {
            console.error('Gagal load koordinat:', e);
        }
    } else {
        console.log("Tidak ada data koordinat di submission.");
    }

    // Event saat user bikin/edit/delete shape
    map.on(L.Draw.Event.CREATED, function(e){
        console.log("Draw created:", e.layer);
        const layer = e.layer;
        drawnItems.clearLayers();
        drawnItems.addLayer(layer);

        if(layer instanceof L.Polyline){
            const latlngs = layer.getLatLngs().map(ll => [ll.lat,ll.lng]);
            document.getElementById('koordinat_latlng').value = JSON.stringify({
                type: 'road',
                coords: latlngs
            });
        }
        if(layer instanceof L.Marker){
            const p = layer.getLatLng();
            document.getElementById('koordinat_latlng').value = JSON.stringify({
                type: 'bridge',
                coord: [p.lat,p.lng]
            });
        }
    });

    map.on('draw:edited draw:deleted', function(){
        console.log("Draw edited/deleted");
        const layers = drawnItems.getLayers();
        if(layers.length > 0){
            const layer = layers[0];
            if(layer instanceof L.Polyline){
                const latlngs = layer.getLatLngs().map(ll => [ll.lat,ll.lng]);
                document.getElementById('koordinat_latlng').value = JSON.stringify({
                    type: 'road',
                    coords: latlngs
                });
            }
            if(layer instanceof L.Marker){
                const p = layer.getLatLng();
                document.getElementById('koordinat_latlng').value = JSON.stringify({
                    type: 'bridge',
                    coord: [p.lat,p.lng]
                });
            }
        } else {
            document.getElementById('koordinat_latlng').value = '';
        }
    });
});
</script>

</x-app-layout>
