<?php

use App\Http\Controllers\MahasiswaController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::apiResource('mahasiswa', MahasiswaController::class);
Route::put('/mahasiswa/{id}/hobi', [MahasiswaController::class, 'updateHobi']);
Route::get('/cicd-test', function () {
    return response()->json([
        'message' => 'CI/CD berhasil!'
    ]);
});
Route::post(
    '/mahasiswa/vector-search',
    [MahasiswaController::class, 'vectorSearch']
);
Route::post(
    '/mahasiswa/generate-embedding',
    [MahasiswaController::class, 'generateEmbedding']
);