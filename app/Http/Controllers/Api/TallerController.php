<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Taller;
use App\Models\ImparteTaller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TallerController extends Controller
{
    public function misTalleres()
    {
        $usuarioId = Auth::id();

        $talleres = DB::table('inscripcion')
            ->join('talleres', 'inscripcion.ID_taller', '=', 'talleres.ID_taller')
            ->leftJoin('imparte_taller', 'talleres.ID_taller', '=', 'imparte_taller.ID_taller')
            ->leftJoin('usuarios as instructores', 'imparte_taller.ID_usuario', '=', 'instructores.ID_usuario')
            ->select(
                'inscripcion.ID_inscripción as id',
                'talleres.ID_taller',
                'talleres.nombre as Taller',
                'talleres.horario as Horario', 
                'instructores.nombre as Maestro',
                'inscripcion.periodo',
                'imparte_taller.ID_imparte'
            )
            ->where('inscripcion.ID_usuario', $usuarioId)
            ->get()
            ->map(function($t) use ($usuarioId) {
                $totalClases = DB::table('asistencias')->where('ID_imparte', $t->ID_imparte)->count();
                $asistenciasCount = DB::table('detalle_asistencias')
                    ->join('asistencias', 'detalle_asistencias.ID_asistencia', '=', 'asistencias.ID_asistencia')
                    ->where('detalle_asistencias.ID_usuario', $usuarioId)
                    ->where('asistencias.ID_imparte', $t->ID_imparte)
                    ->where('detalle_asistencias.entro', 1)
                    ->count();

                $porcentaje = ($totalClases > 0) ? ($asistenciasCount / $totalClases) : 0;
                $estaLiberadaManual = DB::table('constancias')
                    ->where('ID_usuario', $usuarioId)->where('ID_imparte', $t->ID_imparte)->exists();

                return [
                    'id'           => $t->id,
                    'Taller'       => $t->Taller,
                    'Maestro'      => $t->Maestro ?? 'Sin asignar',
                    'Horario'      => $t->Horario ?? 'Horario pendiente',
                    'asistencias'  => $asistenciasCount,
                    'total_clases' => $totalClases,
                    'porcentaje'   => $porcentaje,
                    'elegible'     => ($porcentaje >= 0.8 && $estaLiberadaManual),
                    'liberada'     => $estaLiberadaManual
                ];
            });

        return response()->json($talleres);
    }

    public function getEstadisticasMaestro(Request $request)
    {
        try {
            $usuario = Auth::user();
            $hoy = Carbon::now('America/Mexico_City')->toDateString();

            $talleres_impartidos = DB::table('imparte_taller')
                ->join('talleres', 'imparte_taller.ID_taller', '=', 'talleres.ID_taller')
                ->select('talleres.nombre', 'talleres.ID_taller', 'imparte_taller.ID_imparte', 'talleres.horario')
                ->where('imparte_taller.ID_usuario', $usuario->ID_usuario)
                ->get();

            if ($talleres_impartidos->isEmpty()) return response()->json(['status' => 'error', 'message' => 'No asignado'], 404);

            $respuestaFinal = $talleres_impartidos->map(function($taller) use ($hoy) {
                $totalAlumnos = DB::table('inscripcion')->where('ID_taller', $taller->ID_taller)->count();

                $fechasPasadas = DB::table('detalle_asistencias')
                    ->join('asistencias', 'detalle_asistencias.ID_asistencia', '=', 'asistencias.ID_asistencia')
                    ->where('asistencias.ID_imparte', $taller->ID_imparte)
                    ->where('detalle_asistencias.entro', 1)
                    ->where('detalle_asistencias.fecha', '<', $hoy)
                    ->select('detalle_asistencias.fecha')->distinct()
                    ->orderBy('detalle_asistencias.fecha', 'desc')->take(3)->pluck('fecha')->toArray();
                
                $fechasPasadas = array_reverse($fechasPasadas);
                $datosGrafica = [0, 0, 0, 0];
                
                foreach ($fechasPasadas as $key => $fecha) {
                    $conteo = DB::table('detalle_asistencias')
                        ->join('asistencias', 'detalle_asistencias.ID_asistencia', '=', 'asistencias.ID_asistencia')
                        ->where('asistencias.ID_imparte', $taller->ID_imparte)
                        ->where('detalle_asistencias.fecha', $fecha)->where('detalle_asistencias.entro', 1)->count();
                    $datosGrafica[$key] = ($totalAlumnos > 0) ? (int)round(($conteo / $totalAlumnos) * 100) : 0;
                }

                $conteoHoy = DB::table('detalle_asistencias')
                    ->join('asistencias', 'detalle_asistencias.ID_asistencia', '=', 'asistencias.ID_asistencia')
                    ->where('asistencias.ID_imparte', $taller->ID_imparte)
                    ->where('detalle_asistencias.fecha', $hoy)->where('detalle_asistencias.entro', 1)->count();
                
                $porcentajeHoy = ($totalAlumnos > 0) ? (int)round(($conteoHoy / $totalAlumnos) * 100) : 0;
                $datosGrafica[3] = $porcentajeHoy;

                return [
                    'id_imparte'   => $taller->ID_imparte,
                    'taller'       => $taller->nombre,
                    'totalAlumnos' => (int)$totalAlumnos,
                    'porcentaje'   => $porcentajeHoy,
                    'horario'      => $taller->horario ?? 'No definido',
                    'grafica'      => $datosGrafica
                ];
            });

            return response()->json([
                'status' => 'success',
                'data'   => $respuestaFinal 
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function validarQR(Request $request)
    {
        $request->validate(['token_qr' => 'required', 'ID_imparte' => 'required|integer']);
        return response()->json(['status' => 'success', 'message' => 'Código QR validado', 'data' => ['token' => $request->token_qr, 'hora' => now()->toDateTimeString()]]);
    }

    public function getTalleresDisponibles(Request $request)
    {
        $usuarioId = Auth::id();
        
        // --- NUEVA LÓGICA DE PERIODO ESCOLAR ---
        $mesActual = Carbon::now('America/Mexico_City')->month;
        // Definimos los meses de inscripción: 1 (Enero), 5 (Mayo), 9 (Septiembre)
        $mesesPermitidos = [1, 4, 5, 9];
        
        // Verificamos si estamos en un mes de inscripción
        $inscripcionesAbiertas = in_array($mesActual, $mesesPermitidos);

        if (!$inscripcionesAbiertas) {
            return response()->json([
                'status' => 'success',
                'abiertas' => false,
                'message' => 'El periodo de inscripción está cerrado. Solo disponible en Enero, Mayo y Septiembre.',
                'talleres' => []
            ]);
        }
        // ---------------------------------------

        $talleres = DB::table('talleres as t')
            ->select('t.ID_taller', 't.nombre', 't.detalle', 't.horario', 't.cupo', 't.periodo')
            ->where('t.activo', 1)
            ->whereNotIn('t.ID_taller', function($query) use ($usuarioId) {
                $query->select('ID_taller')->from('inscripcion')->where('ID_usuario', $usuarioId);
            })->get()
            ->map(function($t) {
                $t->inscritos = DB::table('inscripcion')->where('ID_taller', $t->ID_taller)->count();
                return $t;
            })->filter(fn($t) => ($t->cupo - $t->inscritos) > 0)->values();

        return response()->json(['status' => 'success', 'abiertas' => true, 'talleres' => $talleres]);
    }

    public function inscribirAlumno(Request $request)
    {
        $request->validate(['ID_taller' => 'required|integer']);
        $usuario = Auth::user();

        // Validamos también aquí que no se inscriban fuera de tiempo por seguridad
        $mesActual = Carbon::now('America/Mexico_City')->month;
        if (!in_array($mesActual, [1, 5, 9])) {
            return response()->json(['status' => 'error', 'message' => 'Periodo de inscripción finalizado.'], 403);
        }

        $taller = DB::table('talleres')->where('ID_taller', $request->ID_taller)->first();
        if (!$taller) return response()->json(['status' => 'error', 'message' => 'No encontrado'], 404);
        if (DB::table('inscripcion')->where('ID_usuario', $usuario->ID_usuario)->where('ID_taller', $request->ID_taller)->exists()) return response()->json(['status' => 'error', 'message' => 'Ya inscrito'], 400);

        try {
            DB::table('inscripcion')->insert(['periodo' => $taller->periodo ?? '2026', 'fecha' => now()->toDateString(), 'ID_carrera' => 1, 'ID_usuario' => $usuario->ID_usuario, 'ID_taller' => $request->ID_taller]);
            return response()->json(['status' => 'success', 'message' => '¡Inscripción exitosa!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // Agrega este método al final de tu TallerController.php

public function inscribirPorQR(Request $request)
{
    $request->validate([
        'ID_taller' => 'required|integer'
    ]);

    $usuario = Auth::user();
    $mesActual = Carbon::now('America/Mexico_City')->month;

    // 1. Validar Periodo (Enero, Mayo, Septiembre) - (Opcional si quieres dejarlo libre por QR)
    if (!in_array($mesActual, [1, 4, 5, 9])) { // Dejé el 4 para tus pruebas
        return response()->json(['status' => 'error', 'message' => 'Inscripciones cerradas por ahora.'], 403);
    }

    // 2. Verificar si ya está inscrito
    $existe = DB::table('inscripcion')
        ->where('ID_usuario', $usuario->ID_usuario)
        ->where('ID_taller', $request->ID_taller)
        ->exists();

    if ($existe) {
        return response()->json(['status' => 'error', 'message' => 'Ya estás inscrito en este taller.'], 400);
    }

    // 3. Verificar Cupo
    $taller = DB::table('talleres')->where('ID_taller', $request->ID_taller)->first();
    $inscritos = DB::table('inscripcion')->where('ID_taller', $request->ID_taller)->count();

    if (($taller->cupo - $inscritos) <= 0) {
        return response()->json(['status' => 'error', 'message' => 'Lo sentimos, ya no hay cupo.'], 400);
    }

    try {
        DB::table('inscripcion')->insert([
            'periodo'    => $taller->periodo ?? '2026',
            'fecha'      => now()->toDateString(),
            'ID_carrera' => 1, 
            'ID_usuario' => $usuario->ID_usuario,
            'ID_taller'  => $request->ID_taller
        ]);

        return response()->json([
            'status' => 'success', 
            'message' => '¡Inscripción exitosa al taller: ' . $taller->nombre . '!'
        ]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => 'Error al inscribir.'], 500);
    }
}

}