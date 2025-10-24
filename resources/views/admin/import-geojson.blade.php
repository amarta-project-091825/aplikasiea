<x-app-layout>
<x-slot name="header">
    <h2 class="font-semibold text-xl">Import GeoJSON Fleksibel</h2>
</x-slot>

<div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="flex gap-3 items-center mb-3">
        <input type="file" id="geojsonFile" accept=".json,.geojson" class="p-2 border rounded">
        <select id="formSelect" class="p-2 border rounded">
            <option value="{{ $formIdJalan }}">Jalan</option>
            <option value="{{ $formIdJembatan }}">Jembatan</option>
        </select>
        <button id="previewBtn" class="px-3 py-2 bg-sky-600 text-white rounded">Preview</button>
        <button id="submitImport" class="px-3 py-2 bg-emerald-600 text-white rounded">Import ke DB</button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="col-span-1 lg:col-span-2">
            <div id="map" style="height:420px; width:100%; border:1px solid #ccc;"></div>
        </div>
        <div id="mappingPanel" class="col-span-1 p-3 border rounded overflow-auto" style="max-height:420px;">
            <h3 class="font-semibold mb-2">Atribut (wakil 1 feature)</h3>
            <div id="mappingContainer"></div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
(() => {
    let rawData = null;
    let allFeatures = [];
    let previewFeature = null;

    const map = L.map('map').setView([-8.0983, 112.1688], 12);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap & CARTO',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);
    const geoLayerGroup = L.featureGroup().addTo(map);

    const fileInput = document.getElementById('geojsonFile');
    const previewBtn = document.getElementById('previewBtn');
    const submitBtn = document.getElementById('submitImport');
    const mappingContainer = document.getElementById('mappingContainer');
    const formSelect = document.getElementById('formSelect');

    async function getFormFields(formId) {
        try {
            const res = await fetch(`/api/forms/${formId}/fields`);
            if (!res.ok) return [];
            return await res.json();
        } catch (e) {
            console.error('Gagal ambil field form:', e);
            return [];
        }
    }

    function clearPreview() {
        geoLayerGroup.clearLayers();
        mappingContainer.innerHTML = '';
        allFeatures = [];
        previewFeature = null;
    }

    fileInput.addEventListener('change', () => {
        clearPreview();
        const file = fileInput.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            try {
                rawData = JSON.parse(e.target.result);
                if (!rawData.features || !Array.isArray(rawData.features)) {
                    alert('File GeoJSON tidak valid.');
                    rawData = null;
                    return;
                }
                allFeatures = rawData.features;
                previewFeature = allFeatures[0] || null;
                renderMap(allFeatures);
                renderMappingPanel();
            } catch (err) {
                console.error(err);
                alert('File JSON tidak valid.');
            }
        };
        reader.readAsText(file);
    });

    previewBtn.addEventListener('click', () => {
        if (!rawData) {
            alert('Pilih file GeoJSON dulu.');
            return;
        }
        renderMap(allFeatures);
        renderMappingPanel();
    });

    function renderMap(geojson) {
        geoLayerGroup.clearLayers();
        L.geoJSON(geojson, {
            style: f => {
                if (f.geometry?.type === 'LineString') return { color: '#0074D9', weight: 3 };
                if (['Polygon','MultiPolygon'].includes(f.geometry?.type))
                    return { color: '#2ECC40', weight: 2, fillOpacity: 0.2 };
                return {};
            },
            pointToLayer: (f, latlng) => L.circleMarker(latlng, {
                radius: 5, fillColor: '#FF4136', color: '#900', fillOpacity: 0.9
            }),
            onEachFeature: (f, layer) => {
                const p = f.properties || {};
                let popup = `<strong>${p.nama ?? p.name ?? 'Tanpa Nama'}</strong><br>`;
                Object.keys(p).forEach(k => popup += `<small>${k}: ${p[k]}</small><br>`);
                layer.bindPopup(popup);
            }
        }).addTo(geoLayerGroup);

        try {
            const bounds = geoLayerGroup.getBounds();
            if (bounds.isValid()) map.fitBounds(bounds, { maxZoom: 16 });
        } catch { /* biarin */ }
    }

    async function renderMappingPanel() {
        mappingContainer.innerHTML = '';
        if (!previewFeature) {
            mappingContainer.innerHTML = '<p>Tidak ada fitur untuk direview.</p>';
            return;
        }

        const formFields = await getFormFields(formSelect.value);
        const props = previewFeature.properties || {};
        const allKeys = new Set();
        allFeatures.forEach(f => Object.keys(f.properties || {}).forEach(k => allKeys.add(k)));

        const header = document.createElement('div');
        header.innerHTML = `
            <div class="font-semibold mb-2">Preview atribut dari 1 fitur</div>
            <div class="text-xs text-gray-600 mb-3">Mapping otomatis ke field form dari DB</div>`;
        mappingContainer.appendChild(header);

        Array.from(allKeys).forEach(key => {
            const sampleVal = props[key] ?? '';
            const row = document.createElement('div');
            row.className = 'mb-3 p-2 border rounded bg-gray-50';
            row.dataset.prop = key;

            row.innerHTML = `
                <div class="text-sm font-medium mb-1">${key}</div>
                <input type="text" value="${sampleVal}" class="w-full border p-1 mb-1">
                <select class="w-full border p-1 mb-2">
                    <option value="">- tidak pakai -</option>
                    ${formFields.map(f => `<option value="${f}">${f}</option>`).join('')}
                </select>
                <button class="hapus-btn px-2 py-1 bg-red-600 text-white rounded">Hapus</button>
            `;

            row.querySelector('.hapus-btn').addEventListener('click', () => {
                allFeatures.forEach(f => {
                    delete f.properties[key];
                    if (f.mappedProperties) delete f.mappedProperties[key];
                });
                row.remove();
            });

            mappingContainer.appendChild(row);
        });
    }
    
    function applyMapping() {
        const mapConfig = {};
        document.querySelectorAll('#mappingContainer > div[data-prop]').forEach(row => {
            const sourceKey = row.dataset.prop;
            const dropdown = row.querySelector('select');
            const mappedKey = dropdown.value;
            if (mappedKey) mapConfig[sourceKey] = mappedKey;
        });

        allFeatures.forEach(f => {
            f.mappedProperties = {};
            const props = f.properties || {};
            Object.entries(mapConfig).forEach(([sourceKey, targetKey]) => {
                f.mappedProperties[targetKey] = props[sourceKey] ?? '';
            });
        });
    }
    submitBtn.addEventListener('click', () => {
        if (!allFeatures.length) {
            alert('Tidak ada fitur untuk diimport.');
            return;
        }

        applyMapping();

        const payload = allFeatures.map(f => {
            const props = { ...(f.properties || {}), ...(f.mappedProperties || {}) };
            const normalized = {};
            for (const [k, v] of Object.entries(props)) {
                normalized[k.trim().toLowerCase().replace(/\s+/g, '_')] = v;
            }
            return { geometry: f.geometry, mappedProperties: normalized };
        });

        console.log("Geometry sebelum dikirim:", allFeatures.map(f => f.geometry));

        fetch("{{ route('import.process') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                features: payload,
                form_id: formSelect.value
            })
        })
        .then(async res => {
            const data = await res.json();
            if (res.ok) {
                alert(data.message || 'Import selesai.');
                window.location.reload();
            } else {
                alert(data.message || 'Import gagal.');
                console.error(data);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error kirim ke server.');
        });
    });
})();
</script>
</x-app-layout>
