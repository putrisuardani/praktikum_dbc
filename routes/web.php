<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mongo-test', function () {
    app('db')->connection('mongodb')->command(['ping' => 1]);
    return 'MongoDB connected!';
});
