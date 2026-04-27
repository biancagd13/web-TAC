<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use App\Models\Usuario;
use App\Models\Carrera;
use App\Models\Taller;
use App\Models\Rol;
use Illuminate\Http\Request;

class InscripcionController extends Controller
{
    public function index()
    {
        $inscripciones = Inscripcion::with(['usuario', 'carrera', 'taller'])->get();
        return view('inscripciones.index', compact('inscripciones'));
    }

    public function create()
    {
        $rolEstudiante = Rol::where('nombre', 'like', '%Estudiante%')
                            ->orWhere('nombre', 'like', '%Alumno%')
                            ->first();

        $usuarios = Usuario::where('ID_rol', $rolEstudiante->ID_rol)
                           ->where('activo', 1)
                           ->get();
                           
        $carreras = Carrera::where('activo', 1)->get();
        $talleres = Taller::where('activo', 1)->get();

        return view('inscripciones.create', compact('usuarios', 'carreras', 'talleres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'periodo' => 'required|string|max:50',
            'fecha' => 'required|date',
            'ID_usuario' => 'required|exists:usuarios,ID_usuario',
            'ID_carrera' => 'required|exists:carreras,ID_carrera',
            'ID_taller' => 'required|exists:talleres,ID_taller',
        ]);

        // 1. Verificar si ya está inscrito
        $yaInscrito = Inscripcion::where('ID_usuario', $request->ID_usuario)
                                 ->where('ID_taller', $request->ID_taller)
                                 ->exists();
        
        if($yaInscrito) {
            return back()->with('error', 'Ya te encuentras inscrito en este taller.');
        }

        // 2. Verificar cupo
        $taller = Taller::findOrFail($request->ID_taller);
        $totalInscritos = Inscripcion::where('ID_taller', $request->ID_taller)->count();

        if($totalInscritos >= $taller->cupo) {
            return back()->with('error', 'Lo sentimos, el cupo para este taller se ha agotado.');
        }

        // 3. Crear inscripción
        Inscripcion::create($request->only(['periodo', 'fecha', 'ID_carrera', 'ID_usuario', 'ID_taller']));
        
        // --- CAMBIO CLAVE AQUÍ ---
        // Si el usuario es Estudiante (Rol 1), lo regresamos a la página anterior (Explorar)
        if (auth()->user()->ID_rol == 1) {
            return back()->with('exito', '¡Inscripción exitosa! Ya puedes ver este taller en tu perfil.');
        }

        // Si es Admin (Rol 3), lo mandamos a la tabla de gestión como antes
        return redirect()->route('inscripciones.index')->with('exito', 'Inscripción guardada correctamente.');
    }

    public function edit($id)
    {
        $inscripcion = Inscripcion::findOrFail($id);
        
        $rolEstudiante = Rol::where('nombre', 'like', '%Estudiante%')
                            ->orWhere('nombre', 'like', '%Alumno%')
                            ->first();

        $usuarios = Usuario::where('ID_rol', $rolEstudiante->ID_rol)->get();
        $carreras = Carrera::all();
        $talleres = Taller::all();
        
        return view('inscripciones.edit', compact('inscripcion', 'usuarios', 'carreras', 'talleres'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'periodo' => 'required|string',
            'fecha' => 'required|date',
            'ID_usuario' => 'required',
            'ID_carrera' => 'required',
            'ID_taller' => 'required',
        ]);

        $inscripcion = Inscripcion::findOrFail($id);
        $inscripcion->update($request->only(['periodo', 'fecha', 'ID_carrera', 'ID_usuario', 'ID_taller']));
        
        return redirect()->route('inscripciones.index')->with('exito', 'Inscripción actualizada.');
    }

    public function destroy($id)
    {
        $inscripcion = Inscripcion::findOrFail($id);
        $inscripcion->delete();
        return redirect()->route('inscripciones.index')->with('exito', 'Inscripción eliminada.');
    }
}