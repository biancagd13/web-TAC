<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Inscripcion;
use App\Models\DetalleAsistencia;
use App\Models\ImparteTaller;
use App\Models\Usuario;
use App\Models\Taller;
use App\Models\Asistencia; 

class PerfilController extends Controller
{
    public function index()
    {
        $user = Auth::user(); 

        // --- LÓGICA PARA ADMINISTRADOR (ROL 3) - DATOS 100% REALES ---
        if ($user->ID_rol == 3) {
            // 1. Estadísticas Generales (Conteo real de tablas)
            $talleresGestionados = Taller::count();
            $totalEstudiantes = Usuario::where('ID_rol', 1)->count();
            $instructoresActivos = Usuario::where('ID_rol', 2)->count();

            // 2. Datos Reales "Este Mes" 
            // Buscamos registros creados en el mes actual
            $nuevosTalleresMes = Taller::whereMonth('created_at', now()->month)->count();
            $inscripcionesMes = Inscripcion::whereMonth('fecha', now()->month)->count();

            // 3. Tasa de Asistencia Global Real
            // Calculamos: (Total de asistencias marcadas / (Inscripciones totales * Sesiones totales))
            $totalSesionesPasadas = Asistencia::count();
            $asistenciasRegistradas = DetalleAsistencia::count();
            $totalInscritos = Inscripcion::count();

            $tasaAsistencia = ($totalInscritos > 0 && $totalSesionesPasadas > 0)
                ? round(($asistenciasRegistradas / ($totalInscritos * $totalSesionesPasadas)) * 100)
                : 0;

            // 4. Obtener nombre del Rol
            $nombreRol = $user->rol->nombre_rol ?? 'Administrador';

            return view('perfil_admin', compact(
                'user',
                'talleresGestionados',
                'totalEstudiantes',
                'instructoresActivos',
                'nuevosTalleresMes',
                'inscripcionesMes',
                'tasaAsistencia',
                'nombreRol'
            ));
        }

        // --- LÓGICA PARA MAESTRO (ROL 2) ---
        if ($user->ID_rol == 2) {
            $talleresImpartidos = ImparteTaller::where('ID_usuario', $user->ID_usuario)
                ->with('taller')
                ->get();

            $especialidad = $talleresImpartidos->pluck('taller.nombre')->implode(', ') ?: 'Sin talleres asignados';
            $idsTalleres = $talleresImpartidos->pluck('ID_taller');
            $totalMisEstudiantes = Inscripcion::whereIn('ID_taller', $idsTalleres)->count();
            $sesionesImpartidas = Asistencia::whereIn('ID_imparte', $idsTalleres)->count();

            $asistenciasTotalesAlumnos = DetalleAsistencia::whereHas('asistencia', function($q) use ($idsTalleres) {
                    $q->whereIn('ID_imparte', $idsTalleres);
                })->count();
            
            $promedioAsistenciaMaestro = ($totalMisEstudiantes > 0 && $sesionesImpartidas > 0)
                ? round(($asistenciasTotalesAlumnos / ($totalMisEstudiantes * $sesionesImpartidas)) * 100)
                : 0;

            $totalEstudiantesSistema = Usuario::where('ID_rol', 1)->count(); 
            $talleresActivosSistema = Taller::where('activo', 1)->count();

            return view('perfil_maestro', compact(
                'user',
                'talleresImpartidos',
                'especialidad',
                'totalMisEstudiantes',
                'totalEstudiantesSistema',
                'talleresActivosSistema',
                'sesionesImpartidas',
                'promedioAsistenciaMaestro'
            ));
        }

        // --- LÓGICA PARA ESTUDIANTE (ROL 1) ---
        $inscripcionesActivas = Inscripcion::where('ID_usuario', $user->ID_usuario)
            ->whereHas('taller', function($query) {
                $query->where('activo', 1);
            })
            ->with(['taller', 'carrera']) 
            ->get();

        $nombreCarrera = $inscripcionesActivas->first()->carrera->nombre ?? 'Sin carrera asignada';

        $talleresData = $inscripcionesActivas->map(function ($ins) use ($user) {
            $asistenciasCount = DetalleAsistencia::where('ID_usuario', $user->ID_usuario)
                ->whereHas('asistencia', function($q) use ($ins) {
                    $q->where('ID_imparte', $ins->ID_taller); 
                })->count();

            $porcentajeAsistencia = ($asistenciasCount > 0) ? round(($asistenciasCount / 24) * 100) : 0;

            return [
                'nombre' => $ins->taller->nombre,
                'inicial' => strtoupper(substr($ins->taller->nombre, 0, 1)),
                'horario' => $ins->taller->horario,
                'asistencia' => $porcentajeAsistencia,
                'progreso' => $porcentajeAsistencia 
            ];
        });

        $clasesTotales = DetalleAsistencia::where('ID_usuario', $user->ID_usuario)->count();
        $promedioAsistencia = $talleresData->avg('asistencia') ?? 0;

        $logros = [
            'primera_clase' => $clasesTotales > 0,
            'multitaller' => $inscripcionesActivas->count() > 1
        ];

        return view('perfil_alumno', compact(
            'user', 
            'talleresData', 
            'clasesTotales', 
            'promedioAsistencia', 
            'inscripcionesActivas',
            'nombreCarrera',
            'logros'
        ));
    }
}