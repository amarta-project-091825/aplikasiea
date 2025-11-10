@php
$oldFields = old('fields');

$initialFields = [];

if ($oldFields) {
    // kalau string, decode
    $initialFields = is_string($oldFields) ? json_decode($oldFields, true) : $oldFields;
} elseif($form?->fields) {
    // kalau fields dari form sudah array
    $initialFields = is_string($form->fields) ? json_decode($form->fields, true) : $form->fields;
} else {
    $initialFields = [
        ['label'=>'Nama','name'=>'nama','type'=>'text','required'=>true,'placeholder'=>'','options'=>[]],
    ];
}
@endphp


<div x-data="{
    fields: @js($initialFields),

    newField() {
        const field = {label:'',name:'',type:'text',required:false,placeholder:'',options:[],min:null,max:null,mimes_csv:'', value:''};
        this.fields.push(field);
        this.$nextTick(() => {
            if(field.type === 'map_drawer') this.initMap(this.fields.length - 1, field);
        });
    },

    removeField(i){ this.fields.splice(i,1); },
    addOption(i){ this.fields[i].options = this.fields[i].options || []; this.fields[i].options.push(''); },
    removeOption(i,j){ this.fields[i].options.splice(j,1); },

    initMap(i, f) {
    const map = L.map('map_' + i).setView([-8.0983, 112.1688], 12);

    // Tile layer putih (light)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap &copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    const drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    const drawControl = new L.Control.Draw({
        draw: {
            polygon: false,
            circle: false,
            rectangle: false,
            marker: false,
            circlemarker: false,
            polyline: true
        },
        edit: {
            featureGroup: drawnItems
        }
    });
    map.addControl(drawControl);

    map.on(L.Draw.Event.CREATED, function (e) {
        const layer = e.layer;
        drawnItems.clearLayers();
        drawnItems.addLayer(layer);
        const latlngs = layer.getLatLngs().map(ll => [ll.lat, ll.lng]);
        f.value = JSON.stringify(latlngs);
    });

    map.on('draw:edited draw:deleted', function () {
        const layers = drawnItems.getLayers();
        if (layers.length > 0) {
            const latlngs = layers[0].getLatLngs().map(ll => [ll.lat, ll.lng]);
            f.value = JSON.stringify(latlngs);
        } else {
            f.value = '';
        }
    });

    if (f.value) {
        try {
            const coords = JSON.parse(f.value);
            const polyline = L.polyline(coords).addTo(drawnItems);
            map.fitBounds(polyline.getBounds());
        } catch (e) { console.error(e) }
    }
}

}">
    {{-- Meta --}}
    <div class="space-y-6">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900/40 dark:to-gray-800/40 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-[#7f1d1d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Informasi Form
            </h3>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Nama Form <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $form->name ?? '') }}" required
                           class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:border-[#7f1d1d] focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300"
                           placeholder="Masukkan nama form">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Slug <span class="text-xs text-gray-500">(gunakan snake_case)</span>
                    </label>
                    <input type="text" name="slug" value="{{ old('slug', $form->slug ?? '') }}"
                           class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:border-[#7f1d1d] focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300"
                           placeholder="contoh_form">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Deskripsi
                    </label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:border-[#7f1d1d] focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300 resize-none"
                              placeholder="Deskripsi singkat tentang form ini">{{ old('description', $form->description ?? '') }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $form->is_active ?? false) ? 'checked' : '' }}
                               class="w-5 h-5 text-[#7f1d1d] focus:ring-[#7f1d1d]/20 border-gray-300 dark:border-gray-600 rounded">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktifkan Form</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between bg-gradient-to-r from-[#7f1d1d] to-[#5a1515] rounded-xl p-4 text-white">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3 text-[#ffb800]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="font-bold text-lg">Form Fields</h3>
            </div>
            <button type="button" @click="newField()"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#ffb800] to-[#f59e0b] text-white font-semibold rounded-lg hover:from-[#f59e0b] hover:to-[#d97706] transition-all duration-300 transform hover:scale-105 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Field
            </button>
        </div>
    </div>

    <template x-for="(f,i) in fields" :key="i">
        <div class="bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl p-6 mt-6 shadow-md hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                <h4 class="font-bold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-[#7f1d1d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                    Field <span x-text="i+1" class="text-[#7f1d1d]"></span>
                </h4>
                <button type="button" @click="removeField(i)"
                        class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 rounded-lg text-red-700 hover:from-red-100 hover:to-rose-100 transition-all duration-300 text-sm font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus
                </button>
            </div>

            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Label <span class="text-red-500">*</span></label>
                    <input x-model="f.label" required
                           class="w-full px-4 py-2 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:border-[#7f1d1d] focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300"
                           placeholder="Label field">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Name <span class="text-xs text-gray-500">(snake_case)</span> <span class="text-red-500">*</span></label>
                    <input x-model="f.name" required
                           class="w-full px-4 py-2 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:border-[#7f1d1d] focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300"
                           placeholder="nama_field">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tipe <span class="text-red-500">*</span></label>
                    <select x-model="f.type" x-on:change="f.type==='map_drawer' ? $nextTick(()=>initMap(i,f)) : null"
                            class="w-full px-4 py-2 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:border-[#7f1d1d] focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300">
                        @foreach($types as $type)
                            <option value="{{ $type->value }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid sm:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Placeholder <span class="text-xs text-gray-500">(opsional)</span></label>
                    <input x-model="f.placeholder"
                           class="w-full px-4 py-2 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:border-[#7f1d1d] focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300"
                           placeholder="Placeholder text">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Min <span class="text-xs text-gray-500">(number/file KB)</span></label>
                    <input type="number" x-model.number="f.min"
                           class="w-full px-4 py-2 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:border-[#7f1d1d] focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300"
                           placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Max <span class="text-xs text-gray-500">(number/file KB)</span></label>
                    <input type="number" x-model.number="f.max"
                           class="w-full px-4 py-2 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:border-[#7f1d1d] focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300"
                           placeholder="100">
                </div>
            </div>

            <div class="mt-4 space-y-4">
                <label class="inline-flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" x-model="f.required"
                           class="w-5 h-5 text-[#7f1d1d] focus:ring-[#7f1d1d]/20 border-gray-300 dark:border-gray-600 rounded">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Field Wajib Diisi (Required)</span>
                </label>

                <template x-if="['select','radio','checkbox'].includes(f.type)">
                    <div class="bg-gray-50 dark:bg-gray-900/40 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-3">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Options</label>
                            <button type="button" @click="addOption(i)"
                                    class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg text-blue-700 hover:from-blue-100 hover:to-indigo-100 transition-all duration-300 text-sm font-medium">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Tambah Option
                            </button>
                        </div>
                        <template x-for="(opt,j) in (f.options || [])" :key="j">
                            <div class="flex items-center gap-2 mt-2">
                                <input x-model="f.options[j]"
                                       class="flex-1 px-4 py-2 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:border-[#7f1d1d] focus:ring-2 focus:ring-[#7f1d1d]/20 transition-all duration-300"
                                       placeholder="Option value">
                                <button type="button" @click="removeOption(i,j)"
                                        class="px-3 py-2 bg-red-100 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/40 transition-all duration-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="f.type==='file'">
                    <div class="bg-gray-50 dark:bg-gray-900/40 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Upload File</label>
                        <input type="file" :name="'files['+i+']'"
                               class="w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-[#7f1d1d] file:to-[#5a1515] file:text-white hover:file:from-[#991b1b] hover:file:to-[#7f1d1d] transition-all duration-300">
                        <small class="text-xs text-gray-500 dark:text-gray-400 mt-2 block">Gunakan Max sebagai batas ukuran (KB)</small>
                    </div>
                </template>
            </div>

            <!-- MAP DRAWER -->
            <template x-if="f.type==='map_drawer'">
                <div class="mt-4 bg-gray-50 dark:bg-gray-900/40 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" x-text="f.label"></label>
                    <div :id="'map_'+i" class="h-64 w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg mb-2" x-init="initMap(i,f)"></div>
                    <input type="hidden" :name="f.name+'_latlng'" x-model="f.value">
                    <small class="text-xs text-gray-500 dark:text-gray-400">Gambar jalan di peta, koordinat akan otomatis terisi</small>
                </div>
            </template>
        </div>
    </template>

    <input type="hidden" name="fields" x-bind:value="JSON.stringify(fields.map(f => ({
        label: f.label,
        name: f.name,
        type: f.type,
        required: !!f.required,
        placeholder: f.placeholder ?? '',
        options: (['select','radio','checkbox'].includes(f.type) ? (f.options || []).filter(Boolean) : []),
        min: f.min ?? null,
        max: f.max ?? null,
        mimes: (f.type==='file' && f.mimes_csv) ? f.mimes_csv.split(',').map(s=>s.trim()).filter(Boolean) : [],
        value: f.value ?? null
    })))">
</div>

{{-- Leaflet & Draw Plugin --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
