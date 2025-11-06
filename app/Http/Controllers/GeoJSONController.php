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

    // Ambil field dari form
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

        $coords = $geometry['coordinates']; // lng, lat format

        // ✅ Convert to map_drawer format [lat, lng]
        $latlng = array_map(fn($c) => [floatval($c[1]), floatval($c[0])], $coords);

        $dataToSave = [];
        foreach ($fieldNames as $fname) {
            $dataToSave[$fname] = $props[$fname] ?? null;
        }

        // ✅ Simpan koordinat ke field baru
        $dataToSave['koordinat_latlng'] = $latlng;

        // Cari apakah sudah ada record dengan koordinat start yang sama
        $existingRecord = $existingRecords->first(function ($r) use ($latlng) {
            $old = $r->data['koordinat_latlng'] ?? null;
            if (!$old || !is_array($old) || count($old) === 0) return false;

            // Bandingkan titik pertama
            return abs($old[0][0] - $latlng[0][0]) < 0.000001 &&
                   abs($old[0][1] - $latlng[0][1]) < 0.000001;
        });

        if ($existingRecord) {
            // Merge update data
            $existingData = is_string($existingRecord->data)
                ? json_decode($existingRecord->data, true)
                : $existingRecord->data;

            $updated = false;
            foreach ($dataToSave as $key => $value) {
                if ($value !== null && ($existingData[$key] ?? null) !== $value) {
                    $existingData[$key] = $value;
                    $updated = true;
                }
            }

            if ($updated) {
                $existingRecord->update(['data' => $existingData]);
                $count++;
            } else {
                $skipped++;
            }

        } else {
            // Simpan baru
            FormSubmission::create([
                'form_id' => $formId,
                'data' => $dataToSave,
            ]);
            $count++;
        }
    }

    $message = "$count fitur berhasil diimport, $skipped dilewati (invalid/duplikat).";

    if ($request->wantsJson() || $request->isJson()) {
        return response()->json(['success' => true, 'message' => $message]);
    }

    return back()->with('success', $message);
  }
}
