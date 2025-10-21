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
    let features = []; // unique features used for mapping (wakil)
    let allFeatures = []; // full geojson features for map
    let previewFeature = null;

    const map = L.map('map').setView([-8.0983, 112.1688], 12);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap &copy; CARTO', subdomains: 'abcd', maxZoom: 19
    }).addTo(map);

    const geoLayerGroup = L.layerGroup().addTo(map);

    const fileInput = document.getElementById('geojsonFile');
    const previewBtn = document.getElementById('previewBtn');
    const mappingContainer = document.getElementById('mappingContainer');
    const submitBtn = document.getElementById('submitImport');
    const formSelect = document.getElementById('formSelect');

    function clearPreview() {
        geoLayerGroup.clearLayers();
        mappingContainer.innerHTML = '';
        features = [];
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
                    alert('File GeoJSON tidak valid (features tidak ditemukan).');
                    rawData = null;
                    return;
                }
                allFeatures = rawData.features;
                // build unique representative features
                const seen = new Set();
                const unique = [];
                allFeatures.forEach(f => {
                    let key;
                    if (f.geometry && f.geometry.type === 'LineString') {
                        const start = f.geometry.coordinates[0] || [];
                        const end = f.geometry.coordinates[f.geometry.coordinates.length - 1] || [];
                        key = f.properties?.id ?? (start.join(',') + '|' + end.join(','));
                    } else if (f.geometry && f.geometry.type === 'Point') {
                        key = f.properties?.id ?? (f.geometry.coordinates.join(','));
                    } else {
                        key = JSON.stringify(f.properties ?? {}) + '|' + (f.geometry?.type ?? 'unknown');
                    }
                    if (!seen.has(key)) {
                        seen.add(key);
                        unique.push(f);
                    }
                });
                features = unique;
                previewFeature = features[0] || null;

                renderMap(allFeatures);
                renderMappingPanel();
            } catch (err) {
                console.error(err);
                alert('Gagal membaca file (JSON invalid).');
                rawData = null;
            }
        };
        reader.readAsText(file);
    });

    previewBtn.addEventListener('click', () => {
        if (!rawData) {
            alert('Pilih file GeoJSON terlebih dahulu.');
            return;
        }
        renderMap(allFeatures);
        renderMappingPanel();
    });

    function renderMap(geojson) {
        geoLayerGroup.clearLayers();
        const layer = L.geoJSON(geojson, {
            style: (feature) => {
                if (feature.geometry && feature.geometry.type === 'LineString') {
                    return { color: '#0074D9', weight: 3, opacity: 0.8 };
                }
                if (feature.geometry && (feature.geometry.type === 'Polygon' || feature.geometry.type === 'MultiPolygon')) {
                    return { color: '#2ECC40', weight: 2, fillOpacity: 0.2 };
                }
                return {};
            },
            pointToLayer: (feature, latlng) => {
                return L.circleMarker(latlng, { radius: 6, fillColor: '#FF4136', color: '#900', fillOpacity: 0.9 });
            },
            onEachFeature: (feature, layer) => {
                const p = feature.properties || {};
                let popup = `<strong>${p.nama ?? p.name ?? 'Tanpa Nama'}</strong><br>`;
                Object.keys(p).forEach(k => {
                    if (k === '_raw') return;
                    popup += `<small>${k}: ${p[k]}</small><br>`;
                });
                layer.bindPopup(popup);
            }
        }).addTo(geoLayerGroup);
        if (geoLayerGroup.getBounds && !geoLayerGroup.getBounds().isEmpty()) {
            map.fitBounds(geoLayerGroup.getBounds(), { maxZoom: 16 });
        }
    }

    function renderMappingPanel() {
        mappingContainer.innerHTML = '';
        if (!previewFeature) {
            mappingContainer.innerHTML = '<p>Tidak ada fitur untuk direview.</p>';
            return;
        }

        const props = previewFeature.properties || {};
        const header = document.createElement('div');
        header.innerHTML = `<div class="font-semibold mb-2">Preview atribut dari 1 fitur (wakil)</div>
                            <div class="text-xs text-gray-600 mb-3">Edit nilai, pilih mapping tujuan, atau hapus atribut</div>`;
        mappingContainer.appendChild(header);

        // Collect all existing property keys across all features for convenience
        const allKeys = new Set();
        allFeatures.forEach(f => {
            const p = f.properties || {};
            Object.keys(p).forEach(k => allKeys.add(k));
        });

        // For each key show row
        Array.from(allKeys).forEach(key => {
            const sampleVal = previewFeature.properties?.[key] ?? '';
            const row = document.createElement('div');
            row.className = 'mb-2';

            const label = document.createElement('div');
            label.className = 'text-sm font-medium';
            label.innerText = key;
            row.appendChild(label);

            const valInput = document.createElement('input');
            valInput.type = 'text';
            valInput.value = sampleVal;
            valInput.className = 'w-full border p-1 mb-1';
            row.appendChild(valInput);

            const sel = document.createElement('select');
            sel.className = 'w-full border p-1';
            const options = ['', 'nama_jalan','kecamatan_jalan','desa_jalan','status_jalan','kondisi_jalan','tipe_jalan','tahun_pembangunan','lebar_jalan','panjang_jalan','keterangan_jalan','id','nama'];
            options.forEach(o => { const opt = document.createElement('option'); opt.value = o; opt.text = o || '- tidak pakai -'; sel.appendChild(opt); });

            row.appendChild(sel);

            const controls = document.createElement('div');
            controls.className = 'mt-1 flex gap-2';

            const applyBtn = document.createElement('button');
            applyBtn.innerText = 'Apply to all';
            applyBtn.className = 'px-2 py-1 bg-blue-600 text-white rounded';
            applyBtn.addEventListener('click', () => {
                // apply mapping/value to all features' mappedProperties
                allFeatures.forEach(f => {
                    f.mappedProperties = f.mappedProperties || {};
                    const mappedKey = sel.value || key;
                    if (valInput.value === '') {
                        delete f.mappedProperties[mappedKey];
                    } else {
                        f.mappedProperties[mappedKey] = valInput.value;
                    }
                });
                alert('Mapping diterapkan ke semua fitur.');
            });
            controls.appendChild(applyBtn);

            const delBtn = document.createElement('button');
            delBtn.innerText = 'Delete attribute';
            delBtn.className = 'px-2 py-1 bg-red-600 text-white rounded';
            delBtn.addEventListener('click', () => {
                // remove attribute from preview UI and from mappedProperties of each feature
                allFeatures.forEach(f => {
                    if (f.mappedProperties) {
                        // remove any mapped entries that used this key
                        Object.keys(f.mappedProperties).forEach(mpKey => {
                            // if mapping used original property name as key, remove it
                            if (mpKey === key) delete f.mappedProperties[mpKey];
                        });
                    }
                    // also remove raw property so preview no longer shows it
                    if (f.properties && f.properties.hasOwnProperty(key)) {
                        delete f.properties[key];
                    }
                });
                row.remove();
            });
            controls.appendChild(delBtn);

            row.appendChild(controls);
            mappingContainer.appendChild(row);
        });
    }

    submitBtn.addEventListener('click', () => {
        if (!allFeatures || allFeatures.length === 0) {
            alert('Tidak ada fitur untuk diimport.');
            return;
        }

        const chosenForm = formSelect.value;
        // prepare payload: for each unique feature, generate mappedProperties
        const payloadFeatures = allFeatures.map(f => {
            // if user didn't set mappedProperties, try to use properties as-is
            const mp = f.mappedProperties || {};
            // fallback: copy original properties keys into mappedProperties with same key names
            if (Object.keys(mp).length === 0) {
                Object.keys(f.properties || {}).forEach(k => {
                    mp[k] = f.properties[k];
                });
            }
            return {
                geometry: f.geometry,
                mappedProperties: mp
            };
        });

        fetch("{{ route('import.process') }}", {
            method: 'POST',
            headers: {
                'Content-Type':'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                features: payloadFeatures,
                form_id: chosenForm
            })
        }).then(async res => {
            const data = await res.json();
            if (res.ok) {
                alert(data.message || 'Import selesai.');
                // optional: reload page so /api/all will include new data
                window.location.reload();
            } else {
                alert(data.message || 'Import gagal.');
                console.error(data);
            }
        }).catch(err => {
            console.error(err);
            alert('Error saat mengirim ke server.');
        });
    });
})();
</script
</x-app-layout>
