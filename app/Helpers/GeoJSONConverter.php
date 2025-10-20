<?php

namespace App\Helpers;

class GeoJSONConverter
{
    /**
     * Convert a generic FormSubmission record to a GeoJSON Feature.
     * This is flexible: it will use geometry if present; otherwise try to
     * infer from data fields (legacy support).
     *
     * @param \Illuminate\Database\Eloquent\Model|array $record
     * @return array|null
     */
    public static function convertRecord($record)
    {
        // allow both array or Eloquent model
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

        // if geometry exists and is valid, use it
        if (is_array($geometryField) && !empty($geometryField['type']) && !empty($geometryField['coordinates'])) {
            $geom = $geometryField;
        } else {
            // try infer geometry from data fields (legacy)
            $geom = self::inferGeometryFromData($data);
        }

        if (!$geom) return null;

        // Build properties: unify keys for UI consumption
        $props = self::buildPropertiesFromData($data, $formId);
        $props['id'] = (string) ($id ?? ($data['id'] ?? null));

        return [
            'type' => 'Feature',
            'geometry' => $geom,
            'properties' => $props,
        ];
    }

    /**
     * Build a properties array normalized for front-end.
     */
    protected static function buildPropertiesFromData(array $data, $formId = null)
    {
        // Prefer known road/jembatan keys but fall back to generic keys
        $props = [];

        // Try flexible mappings (common variants)
        $mapCandidates = [
            'nama' => ['nama_jalan','nama','road_name','name','nama_jembatan'],
            'kecamatan' => ['kecamatan_jalan','kecamatan','district','kecamatan_jembatan'],
            'desa' => ['desa_jalan','desa','village','desa_jembatan'],
            'status' => ['status_jalan','status'],
            'kondisi' => ['kondisi_jalan','kondisi'],
            'tipe' => ['tipe_jalan','tipe','type'],
            'tahun_pembangunan' => ['tahun_pembangunan','year'],
            'lebar' => ['lebar_jalan','lebar'],
            'panjang' => ['panjang_jalan','panjang','length'],
            'keterangan' => ['keterangan_jalan','keterangan','desc','description'],
        ];

        foreach ($mapCandidates as $key => $variants) {
            foreach ($variants as $v) {
                if (array_key_exists($v, $data) && $data[$v] !== null && $data[$v] !== '') {
                    $props[$key] = $data[$v];
                    break;
                }
            }
            if (!array_key_exists($key, $props)) {
                $props[$key] = null;
            }
        }

        // Also copy all raw data fields as fallback so UI can inspect them
        $props['_raw'] = $data;

        return $props;
    }

    /**
     * Infer geometry from data fields if explicit geometry missing.
     * Returns GeoJSON geometry array or null.
     */
    protected static function inferGeometryFromData(array $data)
    {
        // If lat/lng pair for jembatan exist
        if ((isset($data['latitude_jembatan']) || isset($data['lat'])) &&
            (isset($data['longitude_jembatan']) || isset($data['lng']) || isset($data['longtitude_jembatan']) || isset($data['longitude']))) {

            $lat = $data['latitude_jembatan'] ?? $data['lat'] ?? null;
            $lng = $data['longitude_jembatan'] ?? $data['lng'] ?? $data['longitude'] ?? $data['longtitude_jembatan'] ?? null;

            if (is_numeric($lat) && is_numeric($lng)) {
                return [
                    'type' => 'Point',
                    'coordinates' => [floatval($lng), floatval($lat)]
                ];
            }
        }

        // If route start/end coordinates exist for jalan
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
