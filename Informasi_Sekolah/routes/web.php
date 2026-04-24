<?php

use App\Http\Controllers\LiburController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/libur', [LiburController::class, 'index']);
Route::post('/libur/komentar', [LiburController::class, 'simpanKomentar']);
