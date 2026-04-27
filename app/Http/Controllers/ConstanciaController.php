<?php

namespace App\Http\Controllers;

use App\Models\Constancia;
use App\Models\Usuario;
use App\Models\ImparteTaller;
use App\Models\Rol;
use App\Models\DetalleAsistencia;
use App\Models\Asistencia;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ConstanciaController extends Controller
{
    /**
     * IMPLEMENTACIÓN ACTUALIZADA: 
     * Soporta vistas para Estudiantes (Vista exclusiva), Maestros y Administradores (Paginación).
     */
    public function index(Request $request)
    {
        $usuario = Auth::user();
        
        // --- LÓGICA PARA ESTUDIANTE (ROL 1) ---
        // Se envía a una vista exclusiva sin opciones de edición/borrado
        if ($usuario->ID_rol == 1) {
            $constancias = Constancia::where('ID_usuario', $usuario->ID_usuario)
                ->with(['imparteTaller.taller', 'imparteTaller.usuario', 'detalleConstancia'])
                ->orderBy('fecha_emision', 'desc')
                ->get();

            return view('estudiantes.constancias', compact('constancias'));
        }

        // --- LÓGICA PARA ADMINISTRADOR (ROL 3) ---
        // Se usa paginate() para que el método total() funcione en la vista admin
        if ($usuario->ID_rol == 3) {
            $query = Constancia::with(['usuario', 'imparteTaller.taller', 'detalleConstancia']);

            if ($request->has('buscar') && $request->buscar != '') {
                $buscar = $request->buscar;
                $query->whereHas('detalleConstancia', function($q) use ($buscar) {
                    $q->where('codigo_validacion', 'LIKE', "%$buscar%");
                })->orWhereHas('usuario', function($q) use ($buscar) {
                    $q->where('nombre', 'LIKE', "%$buscar%");
                });
            }

            $constancias = $query->orderBy('fecha_emision', 'desc')->paginate(10);
            return view('constancias.index', compact('constancias'));
        }

        // --- LÓGICA PARA MAESTRO (ROL 2) ---
        $alumnos = Inscripcion::whereIn('ID_taller', function($query) use ($usuario) {
            $query->select('ID_taller')
                  ->from('imparte_taller')
                  ->where('ID_usuario', $usuario->ID_usuario);
        })->with(['usuario', 'taller', 'carrera'])->get();

        foreach ($alumnos as $alumno) {
            $totalSesiones = $alumno->taller->total_sesiones ?? 10; 
            
            $asistenciasAlumno = DetalleAsistencia::where('ID_usuario', $alumno->ID_usuario)
                ->whereHas('asistencia', function($query) use ($alumno, $usuario) {
                    $query->whereHas('imparteTaller', function($q) use ($alumno, $usuario) {
                        $q->where('ID_taller', $alumno->ID_taller)
                          ->where('ID_usuario', $usuario->ID_usuario);
                    });
                })->count();

            $alumno->porcentaje = ($totalSesiones > 0) ? ($asistenciasAlumno / $totalSesiones) * 100 : 0;
        }

        return view('constancias.maestro_index', compact('alumnos'));
    }

    public function create()
    {
        if (Auth::user()->ID_rol != 3) {
            return redirect()->back()->with('error', 'No tienes permiso para esta acción.');
        }

        $usuarios = Usuario::where('ID_rol', 1)->where('activo', 1)->get();
        $imparticiones = ImparteTaller::with(['taller', 'usuario'])->where('activo', 1)->get();

        return view('constancias.create', compact('usuarios', 'imparticiones'));
    }

    public function edit($id)
    {
        if (Auth::user()->ID_rol != 3) {
            return redirect()->back()->with('error', 'No tienes permiso para esta acción.');
        }

        $constancia = Constancia::findOrFail($id);
        $usuarios = Usuario::where('ID_rol', 1)->where('activo', 1)->get();
        $imparticiones = ImparteTaller::with(['taller', 'usuario'])->where('activo', 1)->get();

        return view('constancias.edit', compact('constancia', 'usuarios', 'imparticiones'));
    }

    public function liberar(Request $request)
    {
        $request->validate([
            'ID_usuario' => 'required',
            'ID_taller' => 'required',
        ]);

        $imparte = ImparteTaller::where('ID_taller', $request->ID_taller)
                                ->where('ID_usuario', Auth::user()->ID_usuario)
                                ->first();

        if (!$imparte) {
            return back()->with('error', 'No tienes permiso sobre este taller.');
        }

        Constancia::updateOrCreate(
            [
                'ID_usuario' => $request->ID_usuario,
                'ID_imparte' => $imparte->ID_imparte,
            ],
            [
                'fecha_emision' => now(),
            ]
        );

        return back()->with('exito', '¡Constancia liberada por el instructor correctamente!');
    }

    public function descargarPDF($id)
    {
        $constancia = Constancia::with(['usuario', 'imparteTaller.taller', 'imparteTaller.usuario', 'detalleConstancia'])
                                ->findOrFail($id);

        $totalSesiones = $constancia->imparteTaller->taller->total_sesiones ?? 10; 
        
        $asistenciasAlumno = DetalleAsistencia::where('ID_usuario', $constancia->ID_usuario)
            ->whereHas('asistencia', function($query) use ($constancia) {
                $query->where('ID_imparte', $constancia->ID_imparte);
            })->count();

        $porcentajeAsistencia = ($totalSesiones > 0) ? ($asistenciasAlumno / $totalSesiones) * 100 : 0;

        if (!$constancia->detalleConstancia) {
            return redirect()->back()->with('error', 'La constancia está liberada, pero falta que Administración genere el folio de validación.');
        }

        $detalle = $constancia->detalleConstancia;

        $getB64 = function($url) {
            try {
                $context = stream_context_create([
                    "ssl" => ["verify_peer" => false, "verify_peer_name" => false],
                    "http" => ["header" => "User-Agent: Mozilla/5.0\r\n"]
                ]);
                $content = file_get_contents($url, false, $context);
                return 'data:image/png;base64,' . base64_encode($content);
            } catch (\Exception $e) {
                return ''; 
            }
        };

        $urlEdomex = "https://folios.sisop.edomex.gob.mx/images/edomex-logo-2023.png";
        $urlUtvt   = "https://images.seeklogo.com/logo-png/42/2/universidad-tecnologica-del-valle-de-toluca-logo-png_seeklogo-424940.png";
        $urlQr     = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($detalle->codigo_validacion);

        $data = [
            'alumno'            => $constancia->usuario->nombre,
            'taller'            => $constancia->imparteTaller->taller->nombre,
            'instructor'        => $constancia->imparteTaller->usuario->nombre,
            'fecha'             => \Carbon\Carbon::parse($constancia->fecha_emision)->translatedFormat('d \d\e F \d\e Y'),
            'codigo_validacion' => $detalle->codigo_validacion,
            'firma_digital'     => $detalle->firma_digital,
            'id_constancia'     => $constancia->ID_constancia,
            'porcentaje'        => $porcentajeAsistencia,
            'qr_base64'         => $getB64($urlQr),
            'logo_edomex'       => $getB64($urlEdomex),
            'logo_utvt'         => $getB64($urlUtvt)
        ];

        $pdf = Pdf::loadView('constancias.pdf', $data);
        $pdf->getDomPDF()->set_option("isRemoteEnabled", true);
        $pdf->setPaper('letter', 'portrait');

        return $pdf->download('Constancia_TAC_' . str_replace(' ', '_', $constancia->usuario->nombre) . '.pdf');
    }

    public function store(Request $request) {
        $request->validate([
            'fecha_emision' => 'required|date',
            'ID_usuario' => 'required',
            'ID_imparte' => 'required'
        ]);
        Constancia::create($request->all());
        return redirect()->route('constancias.index')->with('exito', 'Constancia registrada exitosamente.');
    }

    public function update(Request $request, $id) {
        $constancia = Constancia::findOrFail($id);
        $constancia->update($request->all());
        return redirect()->route('constancias.index')->with('exito', 'Datos de la constancia actualizados.');
    }

    public function destroy($id) {
        Constancia::destroy($id);
        return redirect()->route('constancias.index')->with('exito', 'Constancia eliminada correctamente.');
    }
}