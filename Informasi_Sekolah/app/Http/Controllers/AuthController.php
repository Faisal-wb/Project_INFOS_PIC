<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Coba login pakai Auth Laravel
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Set session is_admin jika email admin (backward compatible)
            $isAdmin = false;
            if (Auth::user()->email === 'admin@gmail.com') {
                session(['is_admin' => true]);
                $isAdmin = true;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'user' => Auth::user(),
                    'is_admin' => $isAdmin
                ]);
            }

            return redirect()->intended('/profile');
        }

        if ($request->expectsJson()) {
            return response()->json(['status' => 'error', 'message' => 'Email atau password salah'], 401);
        }

        return back()->with('error', 'Email atau password salah');
    }

    public function showRegister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'no_telp'  => 'nullable|string|max:20',
        ]);

        // Simpan nomor telepon sebagai profil opsional, tapi response register hanya mengembalikan auth data yang dibutuhkan.
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'no_telp'  => $request->no_telp,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Register berhasil',
                'status' => 'success',
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ]);
        }

        return redirect()->route('profile.show')->with('success', 'Registrasi berhasil!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        session()->forget('is_admin');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success']);
        }

        return redirect('/login');
    }
}
