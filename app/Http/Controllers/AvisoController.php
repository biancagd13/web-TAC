<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use App\Models\Usuario;
use App\Models\Inscripcion;
use App\Models\Rol;
use App\Models\Taller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvisoController extends Controller
{
    public function index(Request $request)
    {
        $usuario = Auth::user();
        $buscar = $request->get('buscar');
        $fecha = $request->get('fecha');
        $taller_id = $request->get('taller_id'); // <--- CAPTURAMOS EL NUEVO FILTRO

        if ($usuario->ID_rol == 1) {
            // --- VISTA ESTUDIANTES ---
            
            // 1. FILTRAR AVISOS GENERALES (ADMIN)
            $avisosGenerales = Aviso::whereHas('usuario', function($query) use ($buscar) {
                    $query->where('ID_rol', 3); 
                    // Búsqueda por nombre de usuario en generales
                    if ($buscar) {
                        $query->where('nombre', 'LIKE', "%$buscar%");
                    }
                })
                ->with('usuario')
                ->when($buscar, function($q) use ($buscar) {
                    $q->where(function($sub) use ($buscar) {
                        $sub->where('titulo', 'LIKE', "%$buscar%")
                            ->orWhere('contenido', 'LIKE', "%$buscar%");
                    });
                })
                ->when($fecha, function($q) use ($fecha) {
                    $q->whereDate('fecha_publicacion', $fecha);
                })
                // NUEVA LÓGICA: Si el estudiante filtra por un taller específico, los generales desaparecen (a menos que elija 'general')
                ->when($taller_id, function($q) use ($taller_id) {
                    if ($taller_id && $taller_id !== 'general') {
                        return $q->where('ID_aviso', 0); 
                    }
                })
                ->orderBy('fecha_publicacion', 'desc')
                ->get();

            // 2. FILTRAR AVISOS POR TALLER (INSTRUCTORES)
            $instructoresIds = \App\Models\ImparteTaller::whereIn('ID_taller', function($query) use ($usuario) {
                    $query->select('ID_taller')
                          ->from('inscripcion')
                          ->where('ID_usuario', $usuario->ID_usuario);
                })
                ->pluck('ID_usuario');

            $avisosPorTaller = Aviso::whereIn('ID_usuario', $instructoresIds)
                ->with(['usuario', 'taller'])
                ->when($buscar, function($q) use ($buscar) {
                    $q->where(function($sub) use ($buscar) {
                        $sub->where('titulo', 'LIKE', "%$buscar%")
                            ->orWhere('contenido', 'LIKE', "%$buscar%")
                            // Búsqueda por nombre de instructor
                            ->orWhereHas('usuario', function($u) use ($buscar) {
                                $u->where('nombre', 'LIKE', "%$buscar%");
                            });
                    });
                })
                ->when($fecha, function($q) use ($fecha) {
                    $q->whereDate('fecha_publicacion', $fecha);
                })
                // NUEVA LÓGICA: Filtrar por taller seleccionado o vaciar si elige 'general'
                ->when($taller_id, function($q) use ($taller_id) {
                    if ($taller_id === 'general') {
                        return $q->where('ID_aviso', 0); 
                    }
                    if ($taller_id) {
                        return $q->where('ID_taller', $taller_id);
                    }
                })
                ->orderBy('fecha_publicacion', 'desc')
                ->get();

            $misTalleres = Inscripcion::with('taller')
                ->where('ID_usuario', $usuario->ID_usuario)
                ->get();

            return view('avisos.index', compact('avisosGenerales', 'avisosPorTaller', 'misTalleres', 'buscar', 'fecha'));
        }

        // --- VISTA ADMIN / INSTRUCTOR ---
        $query = Aviso::with(['usuario.rol', 'taller']);

        // Si es Instructor, solo ve sus propios avisos
        if ($usuario->ID_rol == 2) {
            $query->where('ID_usuario', $usuario->ID_usuario);
        }

        // --- NUEVA LÓGICA DE FILTRADO POR TALLER ---
        $query->when($taller_id, function($q) use ($taller_id) {
            if ($taller_id === 'general') {
                return $q->whereNull('ID_taller'); // Avisos del Admin sin taller asignado
            }
            return $q->where('ID_taller', $taller_id); // Avisos de un taller específico
        });

        // Filtros de texto, fecha y USUARIO
        $avisos = $query->when($buscar, function($q) use ($buscar) {
                $q->where(function($sub) use ($buscar) {
                    $sub->where('titulo', 'LIKE', "%$buscar%")
                        ->orWhere('contenido', 'LIKE', "%$buscar%")
                        // Búsqueda por nombre de quien publicó
                        ->orWhereHas('usuario', function($u) use ($buscar) {
                            $u->where('nombre', 'LIKE', "%$buscar%");
                        });
                });
            })
            ->when($fecha, function($q) use ($fecha) {
                $q->whereDate('fecha_publicacion', $fecha);
            })
            ->orderBy('fecha_publicacion', 'desc')
            ->get();

        return view('avisos.index', compact('avisos', 'buscar', 'fecha'));
    }

    public function create()
    {
        return view('avisos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:100',
            'contenido' => 'required|string',
            'fecha_publicacion' => 'required|date',
            'ID_taller' => 'nullable|exists:talleres,ID_taller'
        ]);

        Aviso::create([
            'titulo' => $request->titulo,
            'contenido' => $request->contenido,
            'fecha_publicacion' => $request->fecha_publicacion,
            'ID_usuario' => Auth::user()->ID_usuario,
            'ID_taller' => $request->ID_taller
        ]);

        return redirect()->route('avisos.index')->with('exito', 'Aviso publicado correctamente.');
    }

    public function edit($id)
    {
        $aviso = Aviso::findOrFail($id);
        if ($aviso->ID_usuario != Auth::id()) {
            return redirect()->route('avisos.index')->with('error', 'No autorizado.');
        }
        return view('avisos.edit', compact('aviso'));
    }

    public function update(Request $request, $id)
    {
        $aviso = Aviso::findOrFail($id);
        if ($aviso->ID_usuario != Auth::id()) {
            return redirect()->route('avisos.index')->with('error', 'No autorizado.');
        }

        $request->validate([
            'titulo' => 'required|string|max:100',
            'contenido' => 'required|string',
            'fecha_publicacion' => 'required|date',
        ]);
        
        $aviso->update($request->only(['titulo', 'contenido', 'fecha_publicacion', 'ID_taller']));
        return redirect()->route('avisos.index')->with('exito', 'Aviso actualizado.');
    }

    public function destroy($id)
    {
        $aviso = Aviso::findOrFail($id);
        if (Auth::user()->ID_rol == 3 || $aviso->ID_usuario == Auth::id()) {
            $aviso->delete();
            return redirect()->route('avisos.index')->with('exito', 'Aviso eliminado.');
        }
        return redirect()->route('avisos.index')->with('error', 'No autorizado.');
    }
}