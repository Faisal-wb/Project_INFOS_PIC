<?php

use App\Http\Controllers\LiburController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/libur', [LiburController::class, 'index']);
Route::post('/libur/komentar', [LiburController::class, 'simpanKomentar']);

Route::get('/register', function () {
    return view('register');
});

Route::post('/register', function () {
    // Handle registration logic here
    return redirect('/')->with('success', 'Registrasi berhasil!');
});

