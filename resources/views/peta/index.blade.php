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
            const map = L.map('map').setView([-8.0983, 112.1688], 14);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 19
            }).addTo(map);

            let lastClickedLine = null;

            fetch("{{ url('/api/geojson/all') }}")
                .then(res => res.json())
                .then(data => {
                    if (!data.features || data.features.length === 0) return;

                    data.features.forEach(f => {
                        const geom = f.geometry;
                        const props = f.properties || {};

                        let coords = [];
                        let type = 'road';

                        if (props.koordinat_latlng) {
                            let kl = props.koordinat_latlng;
                            if (typeof kl === 'string') {
                                try { kl = JSON.parse(kl); } catch(e){ kl = {}; }
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

                        // --- Popup content ---
                        let popupContent = '';
                    if (type === 'road' && coords.length > 0) {
                        const start = coords[0];
                        const end = coords[coords.length - 1];
                        popupContent = `<strong>${props.nama_jalan ?? 'Jalan'}</strong><br>
                                        <strong>Start:</strong> [${start[0]}, ${start[1]}]<br>
                                        <strong>End:</strong> [${end[0]}, ${end[1]}]<br>`;
                        Object.entries(props).forEach(([k,v]) => {
                            if (k !== 'koordinat_latlng') popupContent += `<strong>${k}:</strong> ${v}<br>`;
                        });

                        const latlngs = coords.map(c => [c[0], c[1]]);
                        const line = L.polyline(latlngs, { color: '#0074D9', weight: 3, opacity: 0.7 })
                            .bindPopup(popupContent)
                            .addTo(map);

                        // event klik buat highlight
                        line.on('click', function() {
                            // reset warna sebelumnya
                            if (lastClickedLine && lastClickedLine !== line) {
                                lastClickedLine.setStyle({ color: '#0074D9', weight: 3, opacity: 0.7 });
                            }

                            // ubah warna line yang diklik
                            line.setStyle({ color: '#FF4136', weight: 5, opacity: 0.9 }); // warna merah terang
                            lastClickedLine = line;
                        });
                    } else if (type === 'bridge' && coords.length > 0) {
                            const [lat, lng] = coords[0];
                            popupContent = `<strong>${props.nama_jembatan ?? 'Jembatan'}</strong><br>`;
                            Object.entries(props).forEach(([k,v]) => {
                                if (k !== 'koordinat_latlng') popupContent += `<strong>${k}:</strong> ${v}<br>`;
                            });
                            L.marker([lat, lng])
                                .bindPopup(popupContent)
                                .addTo(map);
                        }
                    });
                })
                .catch(err => console.error('Error fetching GeoJSON:', err));
        });
    </script>

</x-app-layout>
