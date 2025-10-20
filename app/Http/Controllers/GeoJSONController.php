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
        $records = FormSubmission::where('form_id', $this->formIdJalan)->get();

        $features = [];
        foreach ($records as $record) {
            $feature = GeoJSONConverter::convertJalan($record);
            if ($feature) {
                $features[] = $feature;
            }
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    public function jembatan()
    {
        $records = FormSubmission::where('form_id', $this->formIdJembatan)->get();

        $features = [];
        foreach ($records as $record) {
            $feature = GeoJSONConverter::convertJembatan($record);
            if ($feature) {
                $features[] = $feature;
            }
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    public function all()
    {
        $records = FormSubmission::whereIn('form_id', [
            $this->formIdJalan,
            $this->formIdJembatan
        ])->get();

        $features = [];
        foreach ($records as $record) {
            if ($record->form_id === $this->formIdJalan) {
                $feature = GeoJSONConverter::convertJalan($record);
            } elseif ($record->form_id === $this->formIdJembatan) {
                $feature = GeoJSONConverter::convertJembatan($record);
            } else {
                continue;
            }

            if ($feature) {
                $features[] = $feature;
            }
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

       public function showImportForm()
    {
        return view('admin.import-geojson', [
            'formIdJalan' => $this->formIdJalan,
            'formIdJembatan' => $this->formIdJembatan
        ]);
    }

    public function importGeoJSON(Request $request)
    {
        $request->validate([
            'features' => 'required|array',
            'form_id' => 'required|string'
        ]);

        $features = $request->input('features');
        $count = 0; $skipped = 0;

        foreach ($features as $f) {
            $geometry = $f['geometry'] ?? null;
            $props = $f['mappedProperties'] ?? null;
            if (!$geometry || !$props) { $skipped++; continue; }

            // Tangani koordinat
            if ($geometry['type'] === 'LineString') {
                $start = $geometry['coordinates'][0];
                $end = end($geometry['coordinates']);
            } else if ($geometry['type'] === 'Point') {
                $start = $end = $geometry['coordinates'];
            }

            $props['latitude_awal'] = $start[1];
            $props['longitude_awal'] = $start[0];
            $props['latitude_akhir'] = $end[1];
            $props['longitude_akhir'] = $end[0];

            // Cek duplikat
            $exists = FormSubmission::where('form_id', $request->form_id)
                        ->where('data.nama_jalan', $props['nama_jalan'] ?? null)
                        ->where('data.latitude_awal', $props['latitude_awal'])
                        ->where('data.longitude_awal', $props['longitude_awal'])
                        ->exists();
            if ($exists) { $skipped++; continue; }

            FormSubmission::create([
                'form_id' => $request->form_id,
                'data' => $props,
                'geometry' => $geometry
            ]);

            $count++;
        }

        return back()->with('success', "$count fitur berhasil diimport, $skipped dilewati.");
    }
}
