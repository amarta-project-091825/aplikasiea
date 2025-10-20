<?php

namespace App\Helpers;

class GeoJSONConverter
{
    // Konversi satu record Jalan
    public static function convertJalan($record)
    {
        $dataField = $record['data'] ?? null;

        if (is_string($dataField)) {
            $data = json_decode($dataField, true);
        } elseif (is_array($dataField)) {
            $data = $dataField;
        } else {
            $data = [];
        }

        if (
            empty($data['latitude_awal']) ||
            empty($data['longitude_awal']) ||
            empty($data['latitude_akhir']) ||
            empty($data['longitude_akhir'])
        ) {
            return null;
        }

        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'LineString',
                'coordinates' => [
                    [
                        floatval($data['longitude_awal']),
                        floatval($data['latitude_awal'])
                    ],
                    [
                        floatval($data['longitude_akhir']),
                        floatval($data['latitude_akhir'])
                    ]
                ]
            ],
            'properties' => [
                'nama' => $data['nama_jalan'] ?? null,
                'kecamatan' => $data['kecamatan_jalan'] ?? null,
                'desa' => $data['desa_jalan'] ?? null,
                'status' => $data['status_jalan'] ?? null,
                'kondisi' => $data['kondisi_jalan'] ?? null,
                'tipe' => $data['tipe_jalan'] ?? null,
                'tahun_pembangunan' => $data['tahun_pembangunan'] ?? null,
                'lebar' => $data['lebar_jalan'] ?? null,
                'panjang' => $data['panjang_jalan'] ?? null,
                'keterangan' => $data['keterangan_jalan'] ?? null,
                'id' => (string) ($record['_id'] ?? null),
            ]
        ];
    }

    // Konversi satu record Jembatan
    public static function convertJembatan($record)
    {
        $dataField = $record['data'] ?? null;

        if (is_string($dataField)) {
            $data = json_decode($dataField, true);
        } elseif (is_array($dataField)) {
            $data = $dataField;
        } else {
            $data = [];
        }

        if (
            empty($data['latitude_jembatan']) ||
            empty($data['longitude_jembatan'])
        ) {
            return null;
        }

        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    floatval($data['longitude_jembatan']),
                    floatval($data['latitude_jembatan'])
                ]
            ],
            'properties' => [
                'nama' => $data['nama_jembatan'] ?? null,
                'kecamatan' => $data['kecamatan_jembatan'] ?? null,
                'desa' => $data['desa_jembatan'] ?? null,
                'kondisi' => $data['kondisi_jembatan'] ?? null,
                'tahun_pembangunan' => $data['tahun_pembangunan_jembatan'] ?? null,
                'panjang' => $data['panjang_jembatan'] ?? null,
                'lebar' => $data['lebar_jembatan'] ?? null,
                'id' => (string) ($record['_id'] ?? null),
            ]
        ];
    }
}
