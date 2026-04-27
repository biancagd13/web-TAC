<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AvisoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'titulo'    => 'required|string|max:100',
            'contenido' => 'required|string',
            'id_imparte' => 'required' 
        ]);

        try {
            $usuarioId = $request->user()->ID_usuario;

            if ($request->id_imparte === 'todos') {
                // Publicar de forma general para todos sus talleres
                DB::table('avisos')->insert([
                    'titulo'            => $request->titulo,
                    'contenido'         => $request->contenido,
                    'fecha_publicacion' => Carbon::now('America/Mexico_City')->toDateString(),
                    'ID_usuario'        => $usuarioId,
                    'ID_taller'         => null, 
                ]);
            } else {
                $idTaller = DB::table('imparte_taller')
                    ->where('ID_imparte', $request->id_imparte)
                    ->value('ID_taller');

                DB::table('avisos')->insert([
                    'titulo'            => $request->titulo,
                    'contenido'         => $request->contenido,
                    'fecha_publicacion' => Carbon::now('America/Mexico_City')->toDateString(),
                    'ID_usuario'        => $usuarioId,
                    'ID_taller'         => $idTaller,
                ]);
            }

            return response()->json(['status' => 'success', 'message' => 'Aviso publicado']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Error en la base de datos',
                'error_detail' => $e->getMessage() 
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $usuario = $request->user();

            $query = DB::table('avisos')
                ->join('usuarios', 'avisos.ID_usuario', '=', 'usuarios.ID_usuario')
                ->leftJoin('talleres', 'avisos.ID_taller', '=', 'talleres.ID_taller')
                ->select(
                    'avisos.*', 
                    'usuarios.nombre as maestro',
                    DB::raw('CASE 
                        WHEN avisos.ID_taller IS NULL AND usuarios.ID_rol = 2 THEN "General"
                        WHEN avisos.ID_taller IS NULL AND usuarios.ID_rol = 3 THEN "Administrador"
                        ELSE talleres.nombre 
                    END as taller_nombre')
                );

            if ($usuario->ID_rol == 1) { // Alumno
                $misTalleresIds = DB::table('inscripcion')
                    ->where('ID_usuario', $usuario->ID_usuario)
                    ->pluck('ID_taller')
                    ->toArray();

                $query->where(function($q) use ($misTalleresIds) {
                    $q->whereIn('avisos.ID_taller', $misTalleresIds)
                      ->orWhereNull('avisos.ID_taller');
                });
            } else if ($usuario->ID_rol == 2) { // Maestro
                // IMPLEMENTACIÓN: Ve sus avisos O los del Administrador (ID_rol 3)
                $query->where(function($q) use ($usuario) {
                    $q->where('avisos.ID_usuario', $usuario->ID_usuario)
                      ->orWhere('usuarios.ID_rol', 3);
                });
            }

            $avisos = $query->orderBy('avisos.ID_aviso', 'desc')->get();
            return response()->json($avisos);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}