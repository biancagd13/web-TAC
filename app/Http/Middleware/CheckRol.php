<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRol
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // 1. Verificamos si el usuario está logueado
        if (!Auth::check()) {
            return redirect('login')->with('error', 'Debes iniciar sesión primero.');
        }

        $user = Auth::user();

        // 2. Verificamos si el usuario está ACTIVO (Regla de oro del sistema)
        if ($user->activo != 1) {
            Auth::logout();
            return redirect('login')->with('error', 'Tu cuenta está inactiva. Contacta al administrador.');
        }

        // 3. Verificamos si su ID_rol está en la lista de permitidos para esta ruta
        // Suponiendo: 1=Admin, 2=Instructor, 3=Estudiante
        if (in_array($user->ID_rol, $roles)) {
            return $next($request);
        }

        // Si no tiene el rol, lo mandamos al inicio con un error
        return redirect('/home')->with('error', 'No tienes permisos para acceder a esta sección.');
    }
}