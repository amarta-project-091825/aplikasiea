<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeoJSONController;
use App\Models\Form;

Route::get('/geojson/jalan', [GeoJSONController::class, 'jalan']);
Route::get('/geojson/jembatan', [GeoJSONController::class, 'jembatan']);
Route::get('/geojson/all', [GeoJSONController::class, 'all']);
Route::get('/test', function () {
    return 'API OK';
});
Route::get('/forms/{id}/fields', function ($id) {
    $form = Form::find($id);
    if (!$form) {
        return response()->json([], 404);
    }

    $fields = is_string($form->fields)
        ? json_decode($form->fields, true)
        : ($form->fields ?? []);

    // Ambil nama field dari array form->fields
    $names = collect($fields)
        ->pluck('name')
        ->filter()
        ->values();

    return response()->json($names);
});

