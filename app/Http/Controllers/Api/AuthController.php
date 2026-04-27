<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Maneja el inicio de sesión desde la App Móvil
     */
    public function login(Request $request)
    {
        // 1. Validamos datos
        $request->validate([
            'correo'   => 'required|email',
            'password' => 'required',
        ]);

        // 2. Buscamos al usuario
        $usuario = Usuario::where('correo', $request->correo)->first();

        // 3. Verificamos credenciales y estado
        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Las credenciales son incorrectas.'
            ], 401);
        }

        if ($usuario->activo != 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tu cuenta está inactiva. Contacta al administrador.'
            ], 403);
        }

        // --- NUEVA IMPLEMENTACIÓN: Obtener LISTA de talleres ---
        $talleres_maestro = [];
        if ($usuario->ID_rol == 2) {
            $talleres_maestro = DB::table('imparte_taller')
                ->where('ID_usuario', $usuario->ID_usuario)
                ->pluck('ID_imparte')
                ->toArray();
        }
        // ------------------------------------------------------

        // 4. Creamos el Token
        $token = $usuario->createToken('auth_token')->plainTextToken;

        // 5. RESPUESTA CON NUEVOS CAMPOS (IMPORTANTE)
        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id'           => $usuario->ID_usuario,
                'nombre'       => $usuario->nombre,
                'rol'          => $usuario->ID_rol,
                'correo'       => $usuario->correo,
                'id_imparte'   => $talleres_maestro[0] ?? null, // Mantenemos compatibilidad con lo viejo
                'ids_imparte'  => $talleres_maestro,           // Nueva implementación: lista completa
                'telefono'     => $usuario->telefono,
                'foto_perfil'  => $usuario->foto_perfil,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Sesión cerrada correctamente.'
        ]);
    }
}
