<?php

use App\Http\Controllers\LiburController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Halaman Utama
Route::get('/', function () {
    return view('welcome');
});

// --- FITUR LOGIN (Punya Kamu) ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/dashboard', function () {
    return "Selamat datang di Dashboard";
});

// --- FITUR LIBUR & REGISTER (Punya Faisall) ---
Route::get('/libur', [LiburController::class, 'index']);
Route::post('/libur/komentar', [LiburController::class, 'simpanKomentar']);

Route::get('/register', function () {
    return view('register');
});

Route::post('/register', function () {
    // Handle registration logic here
    return redirect('/')->with('success', 'Registrasi berhasil!');
});
