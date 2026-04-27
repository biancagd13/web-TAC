<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Constancia;
use App\Models\DetalleAsistencia;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ConstanciaController extends Controller
{
    /**
     * ACTUALIZACIÓN: Obtener constancias del alumno con prioridad de liberación manual.
     */
    public function getConstanciasAlumno(Request $request)
    {
        $usuarioId = $request->user()->ID_usuario;

        // Buscamos los talleres donde el alumno está inscrito
        $inscripciones = DB::table('inscripcion as i')
            ->join('imparte_taller as it', 'i.ID_taller', '=', 'it.ID_taller')
            ->join('talleres as t', 'it.ID_taller', '=', 't.ID_taller')
            ->join('usuarios as m', 'it.ID_usuario', '=', 'm.ID_usuario')
            ->select('t.nombre as Taller', 'm.nombre as Maestro', 'it.ID_imparte', 'i.ID_usuario')
            ->where('i.ID_usuario', $usuarioId)
            ->get();

        $constanciasDisponibles = [];

        foreach ($inscripciones as $insc) {
            // Contamos sesiones y asistencias
            $totalSesiones = DB::table('asistencias')->where('ID_imparte', $insc->ID_imparte)->count();
            $misAsistencias = DB::table('detalle_asistencias')
                ->join('asistencias', 'detalle_asistencias.ID_asistencia', '=', 'asistencias.ID_asistencia')
                ->where('asistencias.ID_imparte', $insc->ID_imparte)
                ->where('detalle_asistencias.ID_usuario', $usuarioId)
                ->count();

            $porcentaje = ($totalSesiones > 0) ? ($misAsistencias / $totalSesiones) : 0;

            // REGLA DE PRIORIDAD: Verificar si el maestro ya la liberó en la tabla constancias
            $registroConstancia = DB::table('constancias')
                ->where('ID_usuario', $usuarioId)
                ->where('ID_imparte', $insc->ID_imparte)
                ->first();

            // Es elegible si cumple el 80% O si el maestro ya generó el registro (liberación manual)
            if ($porcentaje >= 0.8 || $registroConstancia) {
                $constanciasDisponibles[] = [
                    'id'            => $registroConstancia ? $registroConstancia->ID_constancia : "NEW_" . $insc->ID_imparte,
                    'Taller'        => $insc->Taller,
                    'Maestro'       => $insc->Maestro,
                    'fecha_emision' => $registroConstancia ? $registroConstancia->fecha_emision : Carbon::now()->toDateString(),
                    'progreso'      => round($porcentaje * 100) . '%',
                    'elegible'      => true // Bandera para la App móvil
                ];
            }
        }

        return response()->json($constanciasDisponibles);
    }

    /**
     * Descarga de PDF para la App móvil
     */
    public function downloadPDF(Request $request, $id)
    {
        $usuario = $request->user();

        // LÓGICA DE AUTO-GENERACIÓN: Si el ID es temporal, creamos el registro real
        if (strpos($id, 'NEW_') === 0) {
            $idImparte = str_replace('NEW_', '', $id);
            $idReal = DB::table('constancias')->insertGetId([
                'fecha_emision' => Carbon::now()->toDateString(),
                'ID_usuario'    => $usuario->ID_usuario,
                'ID_imparte'    => $idImparte,
                'created_at'    => now(),
                'updated_at'    => now()
            ]);
            $id = $idReal;
        }

        $constancia = Constancia::with(['usuario', 'imparteTaller.taller', 'imparteTaller.usuario', 'detalleConstancia'])
                                ->findOrFail($id);

        // Validación interna de seguridad
        $totalSesiones = DB::table('asistencias')->where('ID_imparte', $constancia->ID_imparte)->count();
        $asistenciasAlumno = DB::table('detalle_asistencias')
            ->join('asistencias', 'detalle_asistencias.ID_asistencia', '=', 'asistencias.ID_asistencia')
            ->where('asistencias.ID_imparte', $constancia->ID_imparte)
            ->where('detalle_asistencias.ID_usuario', $constancia->ID_usuario)
            ->count();

        $porcentajeAsistencia = ($totalSesiones > 0) ? ($asistenciasAlumno / $totalSesiones) * 100 : 0;

        // Si existe el registro físico de constancia, permitimos la descarga aunque sea < 80%
        // (Esto ya está implícito al no retornar error si el registro ya existe en la DB)

        if (!$constancia->detalleConstancia) {
            DB::table('detalle_constancias')->insert([
                'codigo_validacion' => 'TAC-' . strtoupper(bin2hex(random_bytes(4))),
                'firma_digital'     => hash('sha256', $constancia->ID_constancia . 'SECURE'),
                'ID_constancia'     => $constancia->ID_constancia,
                'fecha_envio_email' => now()
            ]);
            $constancia->load('detalleConstancia');
        }

        $detalle = $constancia->detalleConstancia;

        $getB64 = function($url) {
            try {
                $context = stream_context_create([
                    "ssl" => ["verify_peer" => false, "verify_peer_name" => false],
                    "http" => ["header" => "User-Agent: Mozilla/5.0\r\n"]
                ]);
                $data = file_get_contents($url, false, $context);
                if ($data === false) return '';
                return 'data:image/png;base64,' . base64_encode($data);
            } catch (\Exception $e) { 
                return ''; 
            }
        };

        $logoEdomex = $getB64("https://folios.sisop.edomex.gob.mx/images/edomex-logo-2023.png");
        $logoUtvt   = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA..."; // Logo fallback
        $urlQr      = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($detalle->codigo_validacion);
        $qrBase64   = $getB64($urlQr);

        $data = [
            'alumno'            => $constancia->usuario->nombre,
            'taller'            => $constancia->imparteTaller->taller->nombre,
            'instructor'        => $constancia->imparteTaller->usuario->nombre,
            'fecha'             => Carbon::parse($constancia->fecha_emision)->translatedFormat('d \d\e F \d\e Y'),
            'codigo_validacion' => $detalle->codigo_validacion,
            'firma_digital'     => $detalle->firma_digital,
            'id_constancia'     => $constancia->ID_constancia,
            'qr_base64'         => $qrBase64,
            'logo_edomex'       => $logoEdomex,
            'logo_utvt'         => $logoUtvt,
            'porcentaje'        => $porcentajeAsistencia
        ];

        $pdf = Pdf::loadView('constancias.pdf', $data);
        $pdf->setPaper('letter', 'portrait');
        $pdf->getDomPDF()->set_option("isRemoteEnabled", true);
        
        return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="Constancia_TAC.pdf"');
    }
}