<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\GeoJSONConverter;
use App\Models\FormSubmission;

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
        //Log::info('Memulai proses import GeoJSON', $request->all());

        $request->validate([
            'features' => 'required|array',
            'form_id' => 'required|string'
        ]);

        $features = $request->input('features');
        $formId = $request->input('form_id');
        ///Log::info("Import dimulai untuk form_id=$formId, total fitur=".count($features));

        $count = 0; 
        $skipped = 0;

        $form = \App\Models\Form::find($formId);
        $fieldNames = [];
        if ($form) {
            $fields = is_string($form->fields) ? json_decode($form->fields, true) : $form->fields;
            $fieldNames = collect($fields)->pluck('name')->toArray();
            //Log::info("Field names untuk form $formId: ", $fieldNames);
        } else {
            //Log::warning("Form tidak ditemukan untuk ID $formId");
        }

        foreach ($features as $i => $f) {
            //Log::info("Memproses fitur ke-$i");

            $geometry = $f['geometry'] ?? null;
            $props = $f['mappedProperties'] ?? [];

            if (!$geometry || !is_array($geometry)) {
                //Log::warning("Fitur $i dilewati, geometry tidak valid");
                $skipped++;
                continue;
            }

            $geomType = $geometry['type'] ?? null;
            $coords = $geometry['coordinates'] ?? null;
            if (!$geomType || !$coords) {
                //Log::warning("Fitur $i dilewati, type/coordinates kosong");
                $skipped++; 
                continue; 
            }

            if ($geomType === 'LineString') {
                $start = $coords[0];
                $end = end($coords);
            } elseif ($geomType === 'Point') {
                $start = $end = $coords;
            } else {
                $start = $end = is_array($coords) ? (is_array($coords[0]) ? $coords[0] : $coords) : null;
            }

            if (!is_array($start) || !isset($start[0]) || !isset($start[1])) {
                //Log::warning("Fitur $i dilewati, koordinat tidak lengkap");
                $skipped++;
                continue;
            }

            $latStart = is_numeric($start[1]) ? floatval($start[1]) : null;
            $lngStart = is_numeric($start[0]) ? floatval($start[0]) : null;
            $latEnd = is_numeric($end[1]) ? floatval($end[1]) : null;
            $lngEnd = is_numeric($end[0]) ? floatval($end[0]) : null;

            if ($latStart === null || $lngStart === null) {
                //Log::warning("Fitur $i dilewati, koordinat awal tidak valid");
                $skipped++;
                continue;
            }

            $dataToSave = [];
            foreach ($fieldNames as $fname) {
                $dataToSave[$fname] = $props[$fname] ?? null;
            }

            $dataToSave['latitude_awal'] = $latStart;
            $dataToSave['longitude_awal'] = $lngStart;
            $dataToSave['latitude_akhir'] = $latEnd;
            $dataToSave['longitude_akhir'] = $lngEnd;

            //Log::debug("Data yang akan disimpan fitur $i:", $dataToSave);

            $existsQuery = FormSubmission::where('form_id', $formId)
                ->where('data.latitude_awal', $dataToSave['latitude_awal'])
                ->where('data.longitude_awal', $dataToSave['longitude_awal']);

            if (!empty($dataToSave['nama_jalan'])) {
                $existsQuery->where('data.nama_jalan', $dataToSave['nama_jalan']);
            } elseif (!empty($dataToSave['nama'])) {
                $existsQuery->where('data.nama', $dataToSave['nama']);
            }

            if ($existsQuery->exists()) {
                //Log::info("Fitur $i dilewati, data duplikat terdeteksi");
                $skipped++;
                continue;
            }

            FormSubmission::create([
                'form_id' => $formId,
                'data' => $dataToSave,
                'geometry' => $geometry,
            ]);

            $count++;
            //Log::info("Fitur $i berhasil disimpan");
        }

        $message = "$count fitur berhasil diimport, $skipped dilewati (invalid/duplikat).";
        //Log::info("Import selesai. $message");

        if ($request->wantsJson() || $request->isJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
