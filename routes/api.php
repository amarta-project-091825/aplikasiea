<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeoJSONController;

Route::get('/geojson/jalan', [GeoJSONController::class, 'jalan']);
Route::get('/geojson/jembatan', [GeoJSONController::class, 'jembatan']);
Route::get('/geojson/all', [GeoJSONController::class, 'all']);
Route::get('/test', function () {
    return 'API OK';
});
