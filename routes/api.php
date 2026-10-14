<?php

use App\Http\Controllers\MahasiswaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Neo4jController;
use App\Http\Controllers\SpatialController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::post('/mahasiswa/jarak', [SpatialController::class, 'hitungJarak']);

Route::apiResource('mahasiswa', MahasiswaController::class);

// vector search routes
Route::put('/mahasiswa/{id}/hobi', [MahasiswaController::class, 'updateHobi']);
Route::post(
    '/mahasiswa/generate-embedding',
    [MahasiswaController::class, 'generateEmbedding']
);
Route::post(
    '/mahasiswa/vector-search',
    [MahasiswaController::class, 'vectorSearch']
);

Route::put('/mahasiswa/{id}/lokasi', [SpatialController::class, 'updateLokasi']);
Route::post('/mahasiswa/radius', [SpatialController::class, 'dalamRadius']);


// Neo4j routes
Route::get(
    '/mahasiswa/{nim}/makanan',
    [Neo4jController::class, 'makanan']
);
Route::get(
    '/mahasiswa/{nim}/rekomendasi',
    [Neo4jController::class, 'rekomendasi']
);

// cicd test route
Route::get('/cicd-test', function () {
    return response()->json([
        'message' => 'CI/CD berhasil!'
    ]);
});