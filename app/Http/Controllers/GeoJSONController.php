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

    $start = $geometry['coordinates'][0];
    $end = end($geometry['coordinates']);
    $latStart = floatval($start[1]);
    $lngStart = floatval($start[0]);
    $latEnd = isset($end[1]) ? floatval($end[1]) : null;
    $lngEnd = isset($end[0]) ? floatval($end[0]) : null;

    $dataToSave = [];
    foreach ($fieldNames as $fname) {
        $dataToSave[$fname] = $props[$fname] ?? null;
    }
    $dataToSave['latitude_awal'] = $latStart;
    $dataToSave['longitude_awal'] = $lngStart;
    $dataToSave['latitude_akhir'] = $latEnd;
    $dataToSave['longitude_akhir'] = $lngEnd;

    // cari record yang udah ada (berdasarkan koordinat awal)
    $existingRecord = $existingRecords->first(function ($r) use ($latStart, $lngStart) {
        return abs($r->data['latitude_awal'] - $latStart) < 0.000001
            && abs($r->data['longitude_awal'] - $lngStart) < 0.000001;
    });

    if ($existingRecord) {
        $existingData = $existingRecord->data;
        $updated = false;
        foreach ($dataToSave as $key => $value) {
            if (($existingData[$key] ?? null) === null && $value !== null) {
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
        Log::info('Record baru disimpan', ['form_id' => $formId, 'data' => $dataToSave]);
        FormSubmission::create([
            'form_id' => $formId,
            'data' => $dataToSave,
            'geometry' => $geometry,
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
