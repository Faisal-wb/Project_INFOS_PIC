<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($request->email == "admin@gmail.com" && $request->password == "123") {
            return redirect('/dashboard');
        }

        return back()->with('error', 'Email atau password salah');
    }
}
