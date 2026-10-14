<?php

use App\Http\Controllers\MahasiswaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Neo4jController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

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