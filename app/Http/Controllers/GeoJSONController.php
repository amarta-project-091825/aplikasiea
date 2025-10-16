<?php

namespace App\Http\Controllers;

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
}
