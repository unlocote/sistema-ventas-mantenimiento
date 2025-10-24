<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // 👈 Vista única
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $empleado = \App\Models\Empleado::where('username', $credentials['username'])->first();
        if ($empleado && Hash::check($credentials['password'], $empleado->passwordHash)) {
            Auth::guard('empleado')->login($empleado);
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        $cliente = \App\Models\Cliente::where('username', $credentials['username'])->first();
        if ($cliente && Hash::check($credentials['password'], $cliente->passwordHash)) {
            Auth::guard('cliente')->login($cliente);
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        // Si ninguno coincide
        return back()->withErrors([
            'username' => 'Credenciales inválidas o usuario no registrado.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        // Cerrar sesión en ambos guards
        Auth::guard('empleado')->logout();
        Auth::guard('cliente')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}