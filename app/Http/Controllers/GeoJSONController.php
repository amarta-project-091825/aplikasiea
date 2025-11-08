<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\GeoJSONConverter;
use App\Models\FormSubmission;
use Illuminate\Support\Facades\Log;

class GeoJSONController extends Controller
{
    private $formIdJalan = '68d4a500027f9f2a1d04b6f2';
    private $formIdJembatan = '68dc92ef1a9be6494b046b92';

    public function jalan()
    {
        //Log::info('Memuat data jalan...');
        $records = FormSubmission::where('form_id', $this->formIdJalan)->get();
        //Log::info('Jumlah record jalan: '.count($records));

        $features = [];
        foreach ($records as $record) {
            $feature = GeoJSONConverter::convertRecord($record);
            if ($feature) $features[] = $feature;
        }

        //Log::info('Total fitur jalan: '.count($features));
        return response()->json(['type'=>'FeatureCollection','features'=>$features]);
    }

    public function jembatan()
    {
        //Log::info('Memuat data jembatan...');
        $records = FormSubmission::where('form_id', $this->formIdJembatan)->get();
        //Log::info('Jumlah record jembatan: '.count($records));

        $features = [];
        foreach ($records as $record) {
            $feature = GeoJSONConverter::convertRecord($record);
            if ($feature) $features[] = $feature;
        }

        //Log::info('Total fitur jembatan: '.count($features));
        return response()->json(['type'=>'FeatureCollection','features'=>$features]);
    }

    public function all()
    {
        //Log::info('Memuat semua data...');
        $records = FormSubmission::whereIn('form_id', [$this->formIdJalan, $this->formIdJembatan])->get();
        //Log::info('Total record semua: '.count($records));

        $features = [];
        foreach ($records as $record) {
            $feature = GeoJSONConverter::convertRecord($record);
            if ($feature) $features[] = $feature;
        }

        //Log::info('Total fitur hasil gabungan: '.count($features));
        return response()->json(['type'=>'FeatureCollection','features'=>$features]);
    }

    public function showImportForm()
    {
        //Log::info('Menampilkan halaman import GeoJSON');
        return view('admin.import-geojson', [
            'formIdJalan' => $this->formIdJalan,
            'formIdJembatan' => $this->formIdJembatan
        ]);
    }

    public function getFormFields($id)
    {
        //Log::info("Meminta field dari form ID: $id");
        $form = \App\Models\Form::find($id);
        if (!$form) {
            //Log::warning("Form dengan ID $id tidak ditemukan");
            return response()->json([], 404);
        }

        $fields = is_string($form->fields) ? json_decode($form->fields, true) : $form->fields;
        $fieldNames = collect($fields)->pluck('name')->toArray();
        //Log::info("Field ditemukan untuk form $id: ", $fieldNames);

        return response()->json($fieldNames);
    }

    public function importGeoJSON(Request $request)
{
    $request->validate([
        'features' => 'required|array',
        'form_id' => 'required|string'
    ]);

    $features = $request->input('features');
    $formId = $request->input('form_id');

    // Ambil struktur form untuk tahu field mana yang valid
    $form = \App\Models\Form::find($formId);
    $fieldNames = [];
    if ($form) {
        $fields = is_string($form->fields) ? json_decode($form->fields, true) : $form->fields;
        $fieldNames = collect($fields)->pluck('name')->toArray();
    }

    $count = 0;
    $skipped = 0;

    $existingRecords = FormSubmission::where('form_id', $formId)->get();

    foreach ($features as $f) {
        $geometry = $f['geometry'] ?? null;
        $props = $f['mappedProperties'] ?? [];

        if (!$geometry || empty($geometry['coordinates'])) continue;

        $geomType = strtolower($geometry['type']); // Point / LineString / Polygon
        $coords = $geometry['coordinates'];

        // ✅ Convert ke format map_drawer
        $converted = null;

        if ($geomType === 'linestring') {
            // LineString => road
            $latlng = array_map(fn($c) => [floatval($c[1]), floatval($c[0])], $coords);
            $converted = [
                'type' => 'road',
                'coords' => $latlng
            ];
        } elseif ($geomType === 'point') {
            // Point => bridge
            $converted = [
                'type' => 'bridge',
                'coord' => [floatval($coords[1]), floatval($coords[0])]
            ];
        } else {
            // ❌ bentuk selain itu belum di-support
            $skipped++;
            continue;
        }

        // Build data untuk simpan
        $dataToSave = [];
        foreach ($fieldNames as $fname) {
            $dataToSave[$fname] = $props[$fname] ?? null;
        }

        $dataToSave['koordinat_latlng'] = $converted;

        // ✅ Cek apakah data duplikat berdasarkan titik pertama
        $existingRecord = $existingRecords->first(function ($r) use ($converted) {
            $old = $r->data['koordinat_latlng'] ?? null;
            if (!$old) return false;

            if (($old['type'] ?? null) !== ($converted['type'] ?? null)) return false;

            if ($old['type'] === 'road') {
                return abs($old['coords'][0][0] - $converted['coords'][0][0]) < 0.000001 &&
                       abs($old['coords'][0][1] - $converted['coords'][0][1]) < 0.000001;
            }

            if ($old['type'] === 'bridge') {
                return abs($old['coord'][0] - $converted['coord'][0]) < 0.000001 &&
                       abs($old['coord'][1] - $converted['coord'][1]) < 0.000001;
            }

            return false;
        });

        if ($existingRecord) {
            $existingData = is_string($existingRecord->data)
                ? json_decode($existingRecord->data, true)
                : $existingRecord->data;

            $existingData['koordinat_latlng'] = $converted;
            $existingRecord->update(['data' => $existingData]);
            $count++;

        } else {
            FormSubmission::create([
                'form_id' => $formId,
                'data' => $dataToSave,
            ]);
            $count++;
        }
    }

    $message = "$count fitur berhasil diimport, $skipped dilewati.";

    if ($request->wantsJson() || $request->isJson()) {
        return response()->json(['success' => true, 'message' => $message]);
    }

    return back()->with('success', $message);
}

}
