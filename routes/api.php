<?php

use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::apiResource('mahasiswa', MahasiswaController::class);
Route::apiResource('profiles', ProfileController::class);
Route::get('/cicd-test', function () {
    return response()->json([
        'message' => 'CI/CD berhasil!'
    ]);
});