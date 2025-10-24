<?php

namespace App\Helpers;

use App\Models\Form;

class GeoJSONConverter
{
    public static function convertRecord($record)
    {
        $dataField = null;

        if (is_array($record)) {
            $dataField = $record['data'] ?? null;
            $geometryField = $record['geometry'] ?? null;
            $formId = $record['form_id'] ?? null;
            $id = $record['_id'] ?? ($record['id'] ?? null);
        } else {
            $dataField = $record->data ?? null;
            $geometryField = $record->geometry ?? null;
            $formId = $record->form_id ?? null;
            $id = (string) ($record->_id ?? $record->id ?? null);
        }

        // normalize data array
        if (is_string($dataField)) {
            $data = json_decode($dataField, true) ?: [];
        } elseif (is_array($dataField)) {
            $data = $dataField;
        } else {
            $data = [];
        }

        // geometry: pakai seluruh koordinat dari DB jika ada
        if (!empty($geometryField['type']) && !empty($geometryField['coordinates'])) {
            $geom = $geometryField;
        } else {
            // fallback legacy: hanya ambil start-end
            $geom = self::inferGeometryFromData($data);
        }

        if (!$geom) return null;

        $props = self::buildPropertiesFromForm($data, $formId);
        $props['id'] = (string) ($id ?? ($data['id'] ?? null));

        return [
            'type' => 'Feature',
            'geometry' => $geom,
            'properties' => $props,
        ];
    }

    protected static function buildPropertiesFromForm(array $data, $formId = null)
    {
        $props = [];
        $form = $formId ? Form::find($formId) : null;
        $formFields = [];

        if ($form && isset($form->template['fields'])) {
            foreach ($form->template['fields'] as $f) {
                if (isset($f['name'])) $formFields[] = $f['name'];
            }
        }

        if (empty($formFields)) $formFields = array_keys($data);

        foreach ($formFields as $key) {
            $props[$key] = $data[$key] ?? null;
        }

        $props['_raw'] = $data;

        return $props;
    }

    protected static function inferGeometryFromData(array $data)
    {
        // jembatan
        if ((isset($data['latitude_jembatan']) || isset($data['lat'])) &&
            (isset($data['longitude_jembatan']) || isset($data['lng']) || isset($data['longitude']))) {

            $lat = $data['latitude_jembatan'] ?? $data['lat'] ?? null;
            $lng = $data['longitude_jembatan'] ?? $data['lng'] ?? $data['longitude'] ?? null;

            if (is_numeric($lat) && is_numeric($lng)) {
                return [
                    'type' => 'Point',
                    'coordinates' => [floatval($lng), floatval($lat)]
                ];
            }
        }

        // jalan (awal-akhir)
        if ((isset($data['latitude_awal']) || isset($data['lat_start'])) &&
            (isset($data['longitude_awal']) || isset($data['lng_start'])) &&
            (isset($data['latitude_akhir']) || isset($data['lat_end'])) &&
            (isset($data['longitude_akhir']) || isset($data['lng_end']))) {

            $lat1 = $data['latitude_awal'] ?? $data['lat_start'];
            $lng1 = $data['longitude_awal'] ?? $data['lng_start'];
            $lat2 = $data['latitude_akhir'] ?? $data['lat_end'];
            $lng2 = $data['longitude_akhir'] ?? $data['lng_end'];

            if (is_numeric($lat1) && is_numeric($lng1) && is_numeric($lat2) && is_numeric($lng2)) {
                return [
                    'type' => 'LineString',
                    'coordinates' => [
                        [floatval($lng1), floatval($lat1)],
                        [floatval($lng2), floatval($lat2)],
                    ]
                ];
            }
        }

        return null;
    }
}
