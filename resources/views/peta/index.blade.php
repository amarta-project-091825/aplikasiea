<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Peta Infrastruktur') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div id="map" style="height: 500px; width: 100%; border:1px solid #ccc;"></div>
        </div>
    </div>

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    console.log("📍 DOM loaded, initializing map...");

     const mapElement = document.getElementById('map');
    console.log('🧭 map element found?', mapElement); 

    const map = L.map('map').setView([-8.0983, 112.1688], 14);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    let lastClickedLine = null;
    const selectedId = @json($submission ? $submission->_id : null);
    console.log("🔎 Selected submission ID:", selectedId);

    fetch("{{ url('/api/geojson/all') }}")
        .then(res => res.json())
        .then(data => {
            console.log("✅ Fetched GeoJSON:", data);

            if (!data.features || data.features.length === 0) {
                console.warn("⚠️ GeoJSON kosong atau format salah.");
                return;
            }

            let highlightLayer = null;

            data.features.forEach((f, i) => {
                console.log(`🧩 Processing feature #${i}`, f);

                const geom = f.geometry;
                const props = f.properties || {};
                console.log("➡️ Properties:", props);

                let coords = [];
                let type = 'road';

                if (props.koordinat_latlng) {
                    let kl = props.koordinat_latlng;
                    if (typeof kl === 'string') {
                        try { kl = JSON.parse(kl); } catch(e) {
                            console.error("❌ Error parsing koordinat_latlng:", e);
                            kl = {};
                        }
                    }
                    type = kl.type ?? 'road';
                    if (type === 'road') coords = kl.coords ?? [];
                    else if (type === 'bridge') coords = kl.coord ? [kl.coord] : [];
                } else if (geom) {
                    if (geom.type === 'LineString') {
                        type = 'road';
                        coords = geom.coordinates;
                    } else if (geom.type === 'Point') {
                        type = 'bridge';
                        coords = [geom.coordinates];
                    }
                }

                if (coords.length === 0) {
                    console.warn(`⚠️ Feature #${i} (${props.nama_jalan ?? props.nama_jembatan ?? 'unknown'}) tidak punya koordinat.`);
                    return;
                }

                let popupContent = '';
                if (type === 'road') {
                    popupContent = `<strong>${props.nama_jalan ?? 'Jalan'}</strong><br>`;
                    Object.entries(props).forEach(([k,v]) => {
                        if (k !== 'koordinat_latlng') popupContent += `<strong>${k}:</strong> ${v}<br>`;
                    });

                    const latlngs = coords.map(c => [c[0], c[1]]);
                    const isSelected = selectedId && props.submission_id === selectedId;
                    const lineColor = isSelected ? '#FF4136' : '#0074D9';

                    const line = L.polyline(latlngs, { color: lineColor, weight: 3, opacity: 0.8 })
                        .bindPopup(popupContent)
                        .addTo(map);

                    console.log("🛣️ Added line:", props.nama_jalan, "Selected:", isSelected);

                    if (isSelected) highlightLayer = line;

                    line.on('click', function() {
                        console.log("👉 Line clicked:", props.nama_jalan);
                        if (lastClickedLine && lastClickedLine !== line) {
                            lastClickedLine.setStyle({ color: '#0074D9', weight: 3, opacity: 0.7 });
                        }
                        line.setStyle({ color: '#FF4136', weight: 5, opacity: 0.9 });
                        lastClickedLine = line;
                    });
                } else if (type === 'bridge') {
                    const [lat, lng] = coords[0];
                    popupContent = `<strong>${props.nama_jembatan ?? 'Jembatan'}</strong><br>`;
                    Object.entries(props).forEach(([k,v]) => {
                        if (k !== 'koordinat_latlng') popupContent += `<strong>${k}:</strong> ${v}<br>`;
                    });

                    const isSelected = selectedId && props.submission_id === selectedId;
                    const markerColor = isSelected ? 'red' : 'blue';
                    const marker = L.circleMarker([lat, lng], { color: markerColor, radius: 8 })
                        .bindPopup(popupContent)
                        .addTo(map);

                    console.log("🌉 Added marker:", props.nama_jembatan, "Selected:", isSelected);

                    if (isSelected) highlightLayer = marker;

                    marker.on('click', function() {
                        console.log("👉 Marker clicked:", props.nama_jembatan);
                        if (lastClickedLine && lastClickedLine !== marker) {
                            lastClickedLine.setStyle && lastClickedLine.setStyle({ color: '#0074D9', weight: 3, opacity: 0.7 });
                        }
                        marker.setStyle && marker.setStyle({ color: '#FF4136' });
                        lastClickedLine = marker;
                    });
                }
            });

            if (highlightLayer) {
                console.log("🎯 Highlighting layer...");
                map.fitBounds(highlightLayer.getBounds());
                highlightLayer.openPopup();
            } else {
                console.warn("⚠️ Tidak ada layer yang cocok dengan submission_id:", selectedId);
            }
        })
        .catch(err => console.error('💥 Error fetching GeoJSON:', err));
});
</script>


</x-app-layout>
