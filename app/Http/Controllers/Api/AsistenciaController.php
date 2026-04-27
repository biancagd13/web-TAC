<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; 
use Carbon\Carbon;
// LIBRERÍAS PARA REPORTES
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AsistenciasExport;
use Barryvdh\DomPDF\Facade\Pdf;

class AsistenciaController extends Controller
{
    /**
     * MAESTRO: Abre o recupera la sesión de asistencia.
     */
    public function abrirSesion(Request $request)
    {
        try {
            if (!$request->id_imparte) {
                return response()->json(['status' => 'error', 'message' => 'Falta ID Taller'], 400);
            }
            
            $id_imparte = $request->id_imparte;
            $ahora = Carbon::now('America/Mexico_City');
            $hace12Horas = Carbon::now('America/Mexico_City')->subHours(12);

            // IMPORTANTE: Buscamos sesión que coincida EXACTAMENTE con este id_imparte
            $sesionReciente = DB::table('asistencias')
                ->where('ID_imparte', $id_imparte) // Filtro estricto por taller
                ->where('fecha_creacion', '>=', $hace12Horas)
                ->orderBy('ID_asistencia', 'desc')
                ->first();

            if ($sesionReciente) {
                return response()->json([
                    'status' => 'success',
                    'id_asistencia' => $sesionReciente->ID_asistencia,
                    'note' => 'Sesión recuperada para este taller específico',
                    'timestamp' => $ahora->toDateTimeString()
                ]);
            }

            // Si no hay sesión para ESTE taller, creamos una nueva
            $id = DB::table('asistencias')->insertGetId([
                'ID_imparte' => $id_imparte,
                'fecha_creacion' => $ahora
            ]);

            return response()->json([
                'status' => 'success',
                'id_asistencia' => $id,
                'note' => 'Nueva sesión iniciada para este taller',
                'timestamp' => $ahora->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * MAESTRO: Pase de lista manual inteligente.
     */
    public function paseManual(Request $request)
    {
        $request->validate([
            'ID_usuario' => 'required|exists:usuarios,ID_usuario',
            'id_imparte' => 'required|exists:imparte_taller,ID_imparte'
        ]);

        try {
            $hace12Horas = Carbon::now('America/Mexico_City')->subHours(12);
            $sesion = DB::table('asistencias')
                ->where('ID_imparte', $request->id_imparte)
                ->where('fecha_creacion', '>=', $hace12Horas)
                ->orderBy('ID_asistencia', 'desc')
                ->first();

            if (!$sesion) {
                return response()->json(['status' => 'error', 'message' => 'No hay una sesión de QR activa.'], 400);
            }

            $existe = DB::table('detalle_asistencias')
                ->where('ID_asistencia', $sesion->ID_asistencia)
                ->where('ID_usuario', $request->ID_usuario)
                ->exists();

            if ($existe) {
                return response()->json(['status' => 'warning', 'message' => 'El alumno ya tiene asistencia registrada hoy.'], 400);
            }

            DB::table('detalle_asistencias')->insert([
                'fecha'         => Carbon::now('America/Mexico_City'),
                'entro'         => 1,
                'ID_asistencia' => $sesion->ID_asistencia,
                'ID_usuario'    => $request->ID_usuario,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Asistencia marcada con éxito.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * ALUMNO: Registra la asistencia del alumno mediante el QR.
     */
    public function registrar(Request $request)
    {
        if ($request->header('X-App-Secret') !== 'SistemaTAC_2026_Secure') {
            return response()->json(['status' => 'error', 'message' => 'App no autorizada'], 403);
        }
        try {
            $usuarioId = Auth::id();
            $raw_qr = str_replace([' ', '"', "'", '-', '_'], ['', '', '', '+', '/'], $request->qr_data);
            $qr_decodificado = base64_decode($raw_qr);
            $partes = explode('|', $qr_decodificado);

            if (count($partes) < 2 || !str_contains($qr_decodificado, 'TAC_SESSION')) {
                return response()->json(['status' => 'error', 'message' => 'QR no reconocido'], 400);
            }

            $id_sesion_qr = trim($partes[1]);
            $ahora = Carbon::now('America/Mexico_City');
            $sesionInfo = DB::table('asistencias')->where('ID_asistencia', $id_sesion_qr)->first();
            
            if (!$sesionInfo) return response()->json(['status' => 'error', 'message' => 'Sesión no encontrada'], 404);
            if (Carbon::parse($sesionInfo->fecha_creacion)->addHours(12)->isPast()) {
                return response()->json(['status' => 'error', 'message' => 'El código QR ha expirado'], 410);
            }

            $yaExisteRegistro = DB::table('detalle_asistencias')
                ->where('ID_asistencia', $id_sesion_qr)
                ->where('ID_usuario', $usuarioId)
                ->exists();

            if ($yaExisteRegistro) return response()->json(['status' => 'warning', 'message' => 'Ya registrado.'], 400);

            DB::table('detalle_asistencias')->insert([
                'fecha' => $ahora, 'entro' => 1, 'ID_asistencia' => $id_sesion_qr, 'ID_usuario' => $usuarioId,
            ]);
            return response()->json(['status' => 'success', 'message' => 'Asistencia registrada correctamente']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * ALUMNO: Obtiene historial.
     */
    public function historial(Request $request)
    {
        $usuarioId = Auth::id();
        $inscripciones = DB::table('inscripcion as i')
            ->join('talleres as t', 'i.ID_taller', '=', 't.ID_taller')
            ->join('imparte_taller as it', 't.ID_taller', '=', 'it.ID_taller')
            ->join('usuarios as m', 'it.ID_usuario', '=', 'm.ID_usuario')
            ->select('i.ID_taller as id', 't.nombre as Taller', 'm.nombre as Maestro', 'it.ID_imparte')
            ->where('i.ID_usuario', $usuarioId)->get();

        $dataReal = $inscripciones->map(function($item) use ($usuarioId) {
            $totalSesiones = DB::table('asistencias')->where('ID_imparte', $item->ID_imparte)->count();
            $misAsistencias = DB::table('detalle_asistencias')
                ->join('asistencias', 'detalle_asistencias.ID_asistencia', '=', 'asistencias.ID_asistencia')
                ->where('asistencias.ID_imparte', $item->ID_imparte)
                ->where('detalle_asistencias.ID_usuario', $usuarioId)
                ->where('detalle_asistencias.entro', 1)->count();

            $porcentaje = ($totalSesiones > 0) ? ($misAsistencias / $totalSesiones) : 0;
            $liberada = DB::table('constancias')->where('ID_usuario', $usuarioId)->where('ID_imparte', $item->ID_imparte)->exists();

            return [
                'id' => $item->id, 'Taller' => $item->Taller, 'Maestro' => $item->Maestro, 'Horario' => 'Lunes a Viernes',
                'asistencias' => (int)$misAsistencias, 'total_clases' => (int)$totalSesiones, 'porcentaje' => $porcentaje,
                'elegible' => ($porcentaje >= 0.8 || $liberada), 'liberada' => $liberada
            ];
        });
        return response()->json($dataReal);
    }

    /**
     * MAESTRO: Estadísticas.
     */
    public function obtenerEstadisticas($id_imparte)
    {
        try {
            $taller = DB::table('imparte_taller')->where('ID_imparte', $id_imparte)->first();
            if (!$taller) return response()->json(['error' => 'Taller no encontrado'], 404);

            $alumnos = DB::table('usuarios')
                ->join('inscripcion', 'usuarios.ID_usuario', '=', 'inscripcion.ID_usuario')
                ->select('usuarios.ID_usuario', 'usuarios.nombre')
                ->where('inscripcion.ID_taller', $taller->ID_taller)->get();

            $totalSesiones = DB::table('asistencias')->where('ID_imparte', $id_imparte)->count();
            $sumaPorcentajes = 0; $conMas80 = 0;

            $listaAlumnos = $alumnos->map(function($alumno) use ($id_imparte, $totalSesiones, &$conMas80, &$sumaPorcentajes) {
                $asis = DB::table('detalle_asistencias')
                    ->join('asistencias', 'detalle_asistencias.ID_asistencia', '=', 'asistencias.ID_asistencia')
                    ->where('asistencias.ID_imparte', $id_imparte)
                    ->where('detalle_asistencias.ID_usuario', $alumno->ID_usuario)->count();
                $porc = ($totalSesiones > 0) ? ($asis / $totalSesiones) : 0;
                if ($porc >= 0.8) $conMas80++;
                $sumaPorcentajes += $porc;
                return [
                    'ID_usuario' => $alumno->ID_usuario, 'nombre' => $alumno->nombre, 'asistencias' => $asis,
                    'total' => $totalSesiones, 'porcentaje' => $porc, 'elegible' => ($porc >= 0.8)
                ];
            });
            $promedio = (count($alumnos) > 0) ? ($sumaPorcentajes / count($alumnos)) * 100 : 0;
            return response()->json(['totalAlumnos' => count($alumnos), 'promedioAsist' => round($promedio) . '%', 'conMas80' => $conMas80, 'alumnos' => $listaAlumnos]);
        } catch (\Exception $e) { return response()->json(['error' => $e->getMessage()], 500); }
    }

    /**
     * MAESTRO: Generar Concentrado PRO (Sincronizado con Blade)
     */
    public function generarConcentrado(Request $request, $id_imparte)
{
    $tipo = $request->get('periodo', 'cuatrimestral'); 
    $formato = $request->get('formato', 'pdf'); 
    
    $imparte = \App\Models\ImparteTaller::with(['taller', 'usuario'])->where('ID_imparte', $id_imparte)->first();
    if (!$imparte) return response()->json(['status' => 'error', 'message' => 'No encontrado'], 404);

    $columnas = [];
    if ($tipo == 'mensual') {
        $mesActual = Carbon::now()->month;
        $sesiones = DB::table('asistencias')->where('ID_imparte', $id_imparte)
                      ->whereMonth('fecha_creacion', $mesActual)
                      ->orderBy('fecha_creacion', 'asc')->get();
        foreach ($sesiones as $s) {
            $columnas[] = ['id' => $s->ID_asistencia, 'label' => Carbon::parse($s->fecha_creacion)->format('d/m')];
        }
        $tituloFecha = Carbon::now()->translatedFormat('F Y');
    } else {
        $mesActual = Carbon::now()->month;
        $rango = ($mesActual <= 4) ? range(1,4) : (($mesActual <= 8) ? range(5,8) : range(9,12));
        $mesesNombres = [1=>'Ene', 2=>'Feb', 3=>'Mar', 4=>'Abr', 5=>'May', 6=>'Jun', 7=>'Jul', 8=>'Ago', 9=>'Sep', 10=>'Oct', 11=>'Nov', 12=>'Dic'];
        foreach ($rango as $m) { $columnas[] = ['id' => $m, 'label' => $mesesNombres[$m]]; }
        $tituloFecha = "Periodo 2026";
    }

    $alumnos = \App\Models\Inscripcion::with('usuario')->where('ID_taller', $imparte->ID_taller)->get();

    // Nombre del archivo único
    $fileName = 'Reporte_' . $id_imparte . '_' . time() . ($formato === 'excel' ? '.xlsx' : '.pdf');
    // IMPORTANTE: Usamos la carpeta pública temporal de Laravel
    $publicPath = 'reportes/' . $fileName;

    try {
        if ($formato === 'excel') {
            $matriz = [];
            foreach ($alumnos as $al) {
                $fila = ['alumno' => $al->usuario->nombre]; 
                $total = 0;
                foreach ($columnas as $col) {
                    if ($tipo == 'mensual') {
                        $asis = DB::table('detalle_asistencias')->where('ID_asistencia', $col['id'])->where('ID_usuario', $al->ID_usuario)->exists();
                        $fila[$col['label']] = $asis ? 'X' : '-'; 
                        if($asis) $total++;
                    } else {
                        $cant = DB::table('detalle_asistencias')->join('asistencias','detalle_asistencias.ID_asistencia','=','asistencias.ID_asistencia')
                                  ->where('asistencias.ID_imparte',$id_imparte)->whereMonth('asistencias.fecha_creacion',$col['id'])
                                  ->where('detalle_asistencias.ID_usuario',$al->ID_usuario)->count();
                        $fila[$col['label']] = $cant; $total += $cant;
                    }
                }
                $fila['total'] = $total; $matriz[] = $fila;
            }
            $header = "Taller: {$imparte->taller->nombre} | Instructor: {$imparte->usuario->nombre} | {$tituloFecha}";

            // Intentamos guardar en el storage público
            Excel::store(new AsistenciasExport($matriz, $header), $publicPath, 'public', \Maatwebsite\Excel\Excel::XLSX);
        } else {
            // Generación de PDF
            $pdf = Pdf::loadView('asistencias.reporte_mensual', [
                'imparte' => $imparte, 'alumnos' => $alumnos, 'columnas' => $columnas,
                'tipo' => $tipo, 'tituloFecha' => $tituloFecha
            ])->setPaper('letter', 'landscape');

            Storage::disk('public')->put($publicPath, $pdf->output());
        }

        // Si todo salió bien, regresamos el enlace que la App espera
        return response()->json([
            'status' => 'success',
            'url'    => asset('storage/' . $publicPath)
        ]);

    } catch (\Exception $e) {
        // Si falla por permisos, devolvemos el error real para saber qué carpeta crear
        return response()->json([
            'status'  => 'error',
            'message' => 'Error al escribir archivo: ' . $e->getMessage()
        ], 500);
    }
}
    }
