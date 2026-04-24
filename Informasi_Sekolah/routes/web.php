<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', function () {
    return view('register');
});

Route::post('/register', function () {
    // Handle registration logic here
    return redirect('/')->with('success', 'Registrasi berhasil!');
});

