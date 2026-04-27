<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'correo' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['correo' => $request->correo, 'password' => $request->password])) {
            $user = Auth::user();

            if ($user->activo != 1) {
                Auth::logout();
                return back()->withErrors(['correo' => 'Error: Tu cuenta está inactiva.']);
            }

            $request->session()->regenerate();

            // --- REDIRECCIÓN SEGÚN EL ROL (ACTUALIZADO) ---
            
            // Rol 3: Administrador -> Va a Gestión de Talleres
            if ($user->ID_rol == 3) {
                return redirect()->route('talleres.index');
            } 
            
            // Rol 2: Instructor -> ¡AHORA VA A SU PERFIL!
            if ($user->ID_rol == 2) {
                return redirect()->route('perfil'); 
            } 
            
            // Rol 1: Estudiante -> Va a sus Talleres inscritos
            if ($user->ID_rol == 1) {
                return redirect()->route('estudiante.talleres');
            }

            Auth::logout();
            return redirect('/login')->withErrors(['correo' => 'Error: Rol no reconocido.']);
        }

        return back()->withErrors([
            'correo' => 'Error: Las credenciales no coinciden con nuestros registros.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('exito', 'Has salido del sistema correctamente.');
    }
}