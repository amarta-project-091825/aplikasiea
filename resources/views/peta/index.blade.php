<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
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
            // Map: kota Blitar
            const map = L.map('map').setView([-8.0983, 112.1688], 12);

            // Tile layer flat / clean
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 19
            }).addTo(map);

            // Fetch GeoJSON
            fetch("{{ url('/api/geojson') }}")
                .then(res => res.json())
                .then(data => {
                    L.geoJSON(data, {
                        style: feature => {
                            if (feature.geometry.type === 'LineString') {
                                return { color: '#0074D9', weight: 2, opacity: 0.7 }; // jalan
                            }
                            return {};
                        },
                        pointToLayer: (feature, latlng) => {
                            if (feature.geometry.type === 'Point') {
                                return L.circleMarker(latlng, {
                                    radius: 5,
                                    fillColor: 'red',
                                    color: '#900',
                                    weight: 1,
                                    fillOpacity: 0.8
                                }); // jembatan
                            }
                        },
                        onEachFeature: (feature, layer) => {
                            const props = feature.properties;
                            let popupContent = `<strong>${props.nama ?? 'Tanpa Nama'}</strong><br>
                                                Kecamatan: ${props.kecamatan ?? '-'}<br>
                                                Desa: ${props.desa ?? '-'}<br>
                                                Kondisi: ${props.kondisi ?? '-'}<br>`;
                            if (feature.geometry.type === 'LineString') {
                                popupContent += `Tipe: ${props.tipe ?? '-'}<br>
                                                 Tahun: ${props.tahun_pembangunan ?? '-'}<br>
                                                 Panjang: ${props.panjang ?? '-'} m`;
                            } else if (feature.geometry.type === 'Point') {
                                popupContent += `Tahun: ${props.tahun_pembangunan ?? '-'}<br>
                                                 Panjang: ${props.panjang ?? '-'} m`;
                            }
                            layer.bindPopup(popupContent);
                        }
                    }).addTo(map);
                })
                .catch(err => console.error('Error GeoJSON:', err));
        });
    </script>
</x-app-layout>
