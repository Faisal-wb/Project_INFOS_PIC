<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// halaman utama
Route::get('/', function () {
    return view('welcome');
});

// halaman login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

// proses login
Route::post('/login', [AuthController::class, 'login']);

Route::get('/dashboard', function () {
    return "Selamat datang di Dashboard";
});
