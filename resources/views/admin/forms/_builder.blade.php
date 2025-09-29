@php
$initialFields = old('fields') 
    ? json_decode(old('fields'), true)
    : ($form?->fields ?? [
        ['label'=>'Nama','name'=>'nama','type'=>'text','required'=>true,'placeholder'=>'','options'=>[]],
    ]);
@endphp

<div x-data="{
    fields: @js($initialFields),
    newField() { this.fields.push({label:'',name:'',type:'text',required:false,placeholder:'',options:[],min:null,max:null,mimes_csv:''}); },
    removeField(i){ this.fields.splice(i,1); },
    addOption(i){ this.fields[i].options = this.fields[i].options || []; this.fields[i].options.push(''); },
    removeOption(i,j){ this.fields[i].options.splice(j,1); }
}">
    {{-- Meta --}}
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <x-input-label value="Nama Form" />
            <x-text-input name="name" value="{{ old('name', $form->name ?? '') }}" class="mt-1 w-full" required />
        </div>
        <div>
            <x-input-label value="Slug (opsional, auto jika kosong saat create)" />
            <x-text-input name="slug" value="{{ old('slug', $form->slug ?? '') }}" class="mt-1 w-full" />
        </div>
        <div class="sm:col-span-2">
            <x-input-label value="Deskripsi" />
            <textarea name="description" class="mt-1 w-full border rounded p-2" rows="3">{{ old('description', $form->description ?? '') }}</textarea>
        </div>
        <div>
            <label class="inline-flex items-center gap-2 mt-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $form->is_active ?? false) ? 'checked' : '' }}>
                <span>Aktifkan Form</span>
            </label>
        </div>
    </div>

    <hr class="my-6">

    <div class="flex items-center justify-between">
        <h3 class="font-semibold text-lg">Fields</h3>
        <button type="button" class="px-3 py-2 bg-emerald-600 text-white rounded" @click="newField()">+ Tambah Field</button>
    </div>

    <template x-for="(f,i) in fields" :key="i">
        <div class="border rounded p-4 mt-4 bg-gray-50">
            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <x-input-label value="Label" />
                    <input class="mt-1 w-full border rounded p-2" x-model="f.label" required>
                </div>
                <div>
                    <x-input-label value="Name (snake_case)" />
                    <input class="mt-1 w-full border rounded p-2" x-model="f.name" required>
                </div>
                <div>
                    <x-input-label value="Tipe" />
                    <select class="mt-1 w-full border rounded p-2" x-model="f.type">
                        @foreach($types as $type)
                            <option value="{{ $type->value }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid sm:grid-cols-3 gap-4 mt-3">
                <div>
                    <x-input-label value="Placeholder (opsional)" />
                    <input class="mt-1 w-full border rounded p-2" x-model="f.placeholder">
                </div>
                <div>
                    <x-input-label value="Min (number/file size KB)" />
                    <input type="number" class="mt-1 w-full border rounded p-2" x-model.number="f.min">
                </div>
                <div>
                    <x-input-label value="Max (number/file size KB)" />
                    <input type="number" class="mt-1 w-full border rounded p-2" x-model.number="f.max">
                </div>
            </div>

            <div class="flex items-center gap-4 mt-3">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" x-model="f.required">
                    <span>Required</span>
                </label>

                <template x-if="['select','radio','checkbox'].includes(f.type)">
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <x-input-label value="Options" />
                            <button type="button" class="px-2 py-1 bg-indigo-600 text-white rounded" @click="addOption(i)">+ Option</button>
                        </div>
                        <template x-for="(opt,j) in (f.options || [])" :key="j">
                            <div class="flex items-center gap-2 mt-2">
                                <input class="border rounded p-2 w-full" x-model="f.options[j]">
                                <button type="button" class="px-2 py-1 bg-red-600 text-white rounded" @click="removeOption(i,j)">Hapus</button>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="f.type==='file'">
                    <div class="flex-1">
                        <x-input-label value="Upload File" />
                        <input type="file" name="files[{{'${i}'}}]" class="mt-1 w-full border rounded p-2">
                        <small class="text-gray-500">Gunakan Max sebagai batas ukuran (KB).</small>
                    </div>
                </template>
            </div>

            <div class="mt-3 text-right">
                <button type="button" class="px-3 py-2 bg-red-600 text-white rounded" @click="removeField(i)">Hapus Field</button>
            </div>
        </div>
    </template>

    {{-- Hidden input JSON untuk submit --}}
    <input type="hidden" name="fields" x-model="JSON.stringify(fields.map(f => ({
        label: f.label,
        name: f.name,
        type: f.type,
        required: !!f.required,
        placeholder: f.placeholder ?? '',
        options: (['select','radio','checkbox'].includes(f.type) ? (f.options || []).filter(Boolean) : []),
        min: f.min ?? null,
        max: f.max ?? null,
        mimes: (f.type==='file' && f.mimes_csv) ? f.mimes_csv.split(',').map(s=>s.trim()).filter(Boolean) : []
    })))">
</div>
