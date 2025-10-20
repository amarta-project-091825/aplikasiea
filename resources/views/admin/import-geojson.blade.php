<x-app-layout>
<x-slot name="header">
    <h2 class="font-semibold text-xl">Import GeoJSON Fleksibel</h2>
</x-slot>

<div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
    <input type="file" id="geojsonFile" accept=".json,.geojson" class="mb-4 p-2 border rounded">
    <div id="mappingContainer" class="mt-4"></div>
    <button id="submitImport" class="mt-2 px-4 py-2 bg-emerald-600 text-white rounded">Import ke DB</button>

    <div id="map" style="height:400px; width:100%; margin-top:20px;"></div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let features = [];

const map = L.map('map').setView([-8.0983, 112.1688], 12);
L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; OpenStreetMap &copy; CARTO', subdomains: 'abcd', maxZoom: 19
}).addTo(map);

document.getElementById('geojsonFile').addEventListener('change', function(e){
    const file = e.target.files[0];
    const reader = new FileReader();
    reader.onload = function(ev){
        const data = JSON.parse(ev.target.result);

        // Merge 1 feature per objek unik
        let seen = new Set();
        let uniqueFeatures = [];
        data.features.forEach(f=>{
            let key;
            if(f.geometry.type === 'LineString') {
                key = f.properties.id || (f.geometry.coordinates[0] + '-' + f.geometry.coordinates.slice(-1));
            } else {
                key = f.properties.id || f.geometry.coordinates.join(',');
            }
            if(!seen.has(key)){
                uniqueFeatures.push(f);
                seen.add(key);
            }
        });
        features = uniqueFeatures;

        // Bersihkan peta
        map.eachLayer(layer => { if(layer instanceof L.GeoJSON) map.removeLayer(layer); });

        // Preview semua fitur di peta
        L.geoJSON(data, {
            style: f=>f.geometry.type==='LineString'?{color:'#0074D9', weight:2}:{},
            pointToLayer: (f, latlng)=>f.geometry.type==='Point'?L.circleMarker(latlng,{radius:5,fillColor:'red',color:'#900',fillOpacity:0.8}):null
        }).addTo(map);

        // Panel review (wakil 1 feature saja)
        const previewFeature = features[0]; 
        const props = previewFeature.properties;
        const container = document.getElementById('mappingContainer');
        container.innerHTML = '';
        const div = document.createElement('div');
        div.innerHTML = `<h4>Preview Feature (wakil dari semua)</h4>`;

        Object.keys(props).forEach(key => {
            const row = document.createElement('div');
            row.classList.add('flex','items-center','mb-1');

            const label = document.createElement('span'); 
            label.innerText = key + ': '; 
            label.style.width='150px';

            const input = document.createElement('input'); 
            input.type='text'; 
            input.value = props[key]; 
            input.dataset.key = key;

            const select = document.createElement('select');
            const options = ['','nama_jalan','kecamatan_jalan','desa_jalan','status_jalan','kondisi_jalan','tipe_jalan','tahun_pembangunan','lebar_jalan','panjang_jalan','keterangan_jalan','id'];
            options.forEach(opt => { const o=document.createElement('option'); o.value=opt; o.text=opt; select.appendChild(o); });

            const delBtn = document.createElement('button'); 
            delBtn.innerText='❌'; delBtn.type='button';
            delBtn.classList.add('ml-2','px-2','bg-red-500','text-white');
            delBtn.addEventListener('click', () => {
                if(previewFeature.mappedProperties && select.value in previewFeature.mappedProperties){
                    delete previewFeature.mappedProperties[select.value];
                }
                row.remove();
            });

            input.addEventListener('input',()=>{ 
                previewFeature.mappedProperties = previewFeature.mappedProperties || {}; 
                previewFeature.mappedProperties[select.value || key] = input.value;
            });
            select.addEventListener('change',()=>{ 
                previewFeature.mappedProperties = previewFeature.mappedProperties || {}; 
                previewFeature.mappedProperties[select.value || key] = input.value;
            });

            row.appendChild(label); row.appendChild(input); row.appendChild(select); row.appendChild(delBtn);
            div.appendChild(row);
        });
        container.appendChild(div);
    }
    reader.readAsText(file);
});

document.getElementById('submitImport').addEventListener('click',()=>{
    const mappedFeatures = features.map(f=>({geometry:f.geometry,mappedProperties:f.mappedProperties||{}}));
    fetch("{{ route('import.process') }}", {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({features:mappedFeatures,form_id:'{{ $formIdJalan }}'})
    })
    .then(res=>res.json())
    .then(res=>alert('Import selesai!'))
    .catch(err=>console.error(err));
});
</script>
</x-app-layout>
