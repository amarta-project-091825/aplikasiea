@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Peta Kota Blitar</h1>
    <div id="map" style="height: 600px;"></div>
</div>

{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

{{-- Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi Peta
    const map = L.map('map').setView([-8.0983, 112.1688], 13); // Titik tengah Kota Blitar

    // Tambah Tile Layer OSM
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // Ambil GeoJSON dari API
    fetch("{{ url('/api/geojson') }}")
        .then(response => response.json())
        .then(data => {
            L.geoJSON(data, {
                style: function(feature) {
                    if (feature.geometry.type === 'LineString') {
                        return { weight: 3 };
                    }
                },
                pointToLayer: function (feature, latlng) {
                    return L.marker(latlng);
                },
                onEachFeature: function (feature, layer) {
                    let props = feature.properties;
                    layer.bindPopup(
                        `<strong>${props.nama ?? 'Tanpa Nama'}</strong><br>
                        Kecamatan: ${props.kecamatan ?? '-'}<br>
                        Desa: ${props.desa ?? '-'}<br>
                        Kondisi: ${props.kondisi ?? '-'}<br>
                        Tipe: ${props.tipe ?? '-'}`
                    );
                }
            }).addTo(map);
        })
        .catch(error => console.error('Gagal memuat GeoJSON:', error));
});
</script>
@endsection
