<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle proses registrasi user baru
     */
    public function register(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // 2. Simpan user ke database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Autentikasi user (otomatis login setelah daftar)
        Auth::login($user);

        // 4. Return response JSON (karena frontendnya Next.js/React terpisah)
        return response()->json([
            'message' => 'Registrasi berhasil',
            'user' => $user
        ], 201);
    }

    /**
     * Handle autentikasi user
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            // ✅ Redirect ke dashboard, bukan return JSON
            return redirect()->intended('/dashboard');
        }

        throw ValidationException::withMessages([
            'email' => ['Kredensial / Password salah. Silakan coba lagi.'],
        ]);
    }

    /**
     * Handle keluar/logout
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ✅ Redirect ke home, bukan return JSON
        return redirect('/');
    }
}
