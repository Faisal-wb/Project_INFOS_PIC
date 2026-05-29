<?php

use App\Http\Controllers\LiburController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminLiburController;
use App\Http\Controllers\AdminKegiatanController;
use App\Http\Controllers\AdminRapotController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\ProfileController;

// --- HALAMAN BERANDA (Pengumuman Terbaru + Menu Kategori) ---
Route::get('/', [BerandaController::class, 'index'])->name('beranda');

// --- HALAMAN DETAIL INFO (Public) ---
Route::get('/info/libur', [BerandaController::class, 'semuaLibur'])->name('info.libur.semua');
Route::get('/info/libur/{id}', [BerandaController::class, 'showLibur'])->name('info.libur.show');
Route::get('/info/kegiatan', [BerandaController::class, 'semuaKegiatan'])->name('info.kegiatan.semua');
Route::get('/info/kegiatan/{id}', [BerandaController::class, 'showKegiatan'])->name('info.kegiatan.show');
Route::get('/info/rapot', [BerandaController::class, 'semuaRapot'])->name('info.rapot.semua');
Route::get('/info/rapot/{id}', [BerandaController::class, 'showRapot'])->name('info.rapot.show');

// --- FITUR AUTH ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    return "Selamat datang di Dashboard";
});

// --- FITUR PROFIL (Butuh Login) ---
Route::prefix('profile')->name('profile.')->middleware('cek_login')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');
    Route::post('/foto', [ProfileController::class, 'updateFoto'])->name('update.foto');
    Route::delete('/foto', [ProfileController::class, 'deleteFoto'])->name('delete.foto');
});

// --- FITUR LIBUR & KOMENTAR (Punya Faisall) ---
Route::get('/libur', [LiburController::class, 'index']);
Route::post('/libur/komentar', [LiburController::class, 'simpanKomentar']);

// --- FITUR ADMIN (Butuh Login Admin) ---
Route::middleware('cek_admin')->group(function () {
    // Admin Libur
    Route::prefix('admin/libur')->name('admin.libur.')->group(function () {
        Route::get('/', [AdminLiburController::class, 'index'])->name('index');
        Route::get('/create', [AdminLiburController::class, 'create'])->name('create');
        Route::post('/', [AdminLiburController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminLiburController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminLiburController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminLiburController::class, 'destroy'])->name('destroy');
    });

    // Admin Kegiatan Sekolah
    Route::prefix('admin/kegiatan')->name('admin.kegiatan.')->group(function () {
        Route::get('/', [AdminKegiatanController::class, 'index'])->name('index');
        Route::get('/create', [AdminKegiatanController::class, 'create'])->name('create');
        Route::post('/', [AdminKegiatanController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminKegiatanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminKegiatanController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminKegiatanController::class, 'destroy'])->name('destroy');
    });

    // Admin Jadwal Rapot
    Route::prefix('admin/rapot')->name('admin.rapot.')->group(function () {
        Route::get('/', [AdminRapotController::class, 'index'])->name('index');
        Route::get('/create', [AdminRapotController::class, 'create'])->name('create');
        Route::post('/', [AdminRapotController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminRapotController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminRapotController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminRapotController::class, 'destroy'])->name('destroy');
    });
});
