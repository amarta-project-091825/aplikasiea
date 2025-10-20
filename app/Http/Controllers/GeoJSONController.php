<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\GeoJSONConverter;
use App\Models\FormSubmission;

class GeoJSONController extends Controller
{
    private $formIdJalan = '68d4a500027f9f2a1d04b6f2';
    private $formIdJembatan = '68dc92ef1a9be6494b046b92';

    // Return FeatureCollection of jalan only
    public function jalan()
    {
        $records = FormSubmission::where('form_id', $this->formIdJalan)->get();
        $features = [];
        foreach ($records as $record) {
            $feature = GeoJSONConverter::convertRecord($record);
            if ($feature) $features[] = $feature;
        }
        return response()->json(['type'=>'FeatureCollection','features'=>$features]);
    }

    // Return FeatureCollection of jembatan only
    public function jembatan()
    {
        $records = FormSubmission::where('form_id', $this->formIdJembatan)->get();
        $features = [];
        foreach ($records as $record) {
            $feature = GeoJSONConverter::convertRecord($record);
            if ($feature) $features[] = $feature;
        }
        return response()->json(['type'=>'FeatureCollection','features'=>$features]);
    }

    // Return all
    public function all()
    {
        $records = FormSubmission::whereIn('form_id', [$this->formIdJalan, $this->formIdJembatan])->get();
        $features = [];
        foreach ($records as $record) {
            $feature = GeoJSONConverter::convertRecord($record);
            if ($feature) $features[] = $feature;
        }
        return response()->json(['type'=>'FeatureCollection','features'=>$features]);
    }

    // Show import form
    public function showImportForm()
    {
        return view('admin.import-geojson', [
            'formIdJalan' => $this->formIdJalan,
            'formIdJembatan' => $this->formIdJembatan
        ]);
    }

    /**
     * Import GeoJSON. Expect features: [
     *   { geometry: {...}, mappedProperties: { field: value, ... } }, ...
     * ]
     * And form_id chosen by user.
     */
    public function importGeoJSON(Request $request)
    {
        $request->validate([
            'features' => 'required|array',
            'form_id' => 'required|string'
        ]);

        $features = $request->input('features');
        $formId = $request->input('form_id');
        $count = 0; $skipped = 0;

        foreach ($features as $f) {
            $geometry = $f['geometry'] ?? null;
            $props = $f['mappedProperties'] ?? null;

            if (!$geometry || !is_array($geometry) || !$props || !is_array($props)) {
                $skipped++;
                continue;
            }

            // Normalize geometry type & coords
            $geomType = $geometry['type'] ?? null;
            $coords = $geometry['coordinates'] ?? null;
            if (!$geomType || !$coords) { $skipped++; continue; }

            if ($geomType === 'LineString') {
                $start = $coords[0];
                $end = end($coords);
            } else if ($geomType === 'Point') {
                $start = $end = $coords;
            } else {
                // accept Polygon/MultiPolygon by saving geometry as-is
                $start = $end = is_array($coords) ? (is_array($coords[0]) ? $coords[0] : $coords) : null;
            }
            if (!is_array($start) || !isset($start[0]) || !isset($start[1])) {
                $skipped++; continue;
            }

            // Ensure numeric lat/lng
            $latStart = is_numeric($start[1]) ? floatval($start[1]) : null;
            $lngStart = is_numeric($start[0]) ? floatval($start[0]) : null;
            $latEnd = is_numeric($end[1]) ? floatval($end[1]) : null;
            $lngEnd = is_numeric($end[0]) ? floatval($end[0]) : null;

            if ($latStart === null || $lngStart === null) { $skipped++; continue; }

            // Prepare data payload (saved into 'data' column)
            // We keep mappedProperties as-is but also add normalized lat/lng fields for app
            $dataToSave = $props;
            // canonical keys used by app (safe even if importing jembatan)
            $dataToSave['latitude_awal'] = $latStart;
            $dataToSave['longitude_awal'] = $lngStart;
            $dataToSave['latitude_akhir'] = $latEnd;
            $dataToSave['longitude_akhir'] = $lngEnd;

            // Duplicate check: based on form + start coordinate
            $existsQuery = FormSubmission::where('form_id', $formId)
                ->where('data.latitude_awal', $dataToSave['latitude_awal'])
                ->where('data.longitude_awal', $dataToSave['longitude_awal']);

            // if name present, include in duplicate check (works for both jalan/jembatan)
            if (!empty($dataToSave['nama_jalan'])) {
                $existsQuery->where('data.nama_jalan', $dataToSave['nama_jalan']);
            } elseif (!empty($dataToSave['nama'])) {
                $existsQuery->where('data.nama', $dataToSave['nama']);
            }

            if ($existsQuery->exists()) { $skipped++; continue; }

            FormSubmission::create([
                'form_id' => $formId,
                'data' => $dataToSave,
                'geometry' => $geometry
            ]);

            $count++;
        }

        $message = "$count fitur berhasil diimport, $skipped dilewati (invalid/duplikat).";

        // If JSON request, return JSON
        if ($request->wantsJson() || $request->isJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
