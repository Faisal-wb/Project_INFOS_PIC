<?php

use App\Http\Controllers\LiburController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminLiburController;

// Halaman Utama
Route::get('/', function () {
    return view('welcome');
});

// --- FITUR LOGIN (Punya Kamu) ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', function() {
    session()->forget('is_admin');
    return redirect('/login');
})->name('logout');
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

// --- FITUR ADMIN LIBUR (Bagian Kamu) ---
Route::prefix('admin/libur')->name('admin.libur.')->middleware('cek_admin')->group(function () {
    Route::get('/', [AdminLiburController::class, 'index'])->name('index');
    Route::get('/create', [AdminLiburController::class, 'create'])->name('create');
    Route::post('/', [AdminLiburController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [AdminLiburController::class, 'edit'])->name('edit');
    Route::put('/{id}', [AdminLiburController::class, 'update'])->name('update');
    Route::delete('/{id}', [AdminLiburController::class, 'destroy'])->name('destroy');
});
