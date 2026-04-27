<?php

namespace App\Http\Controllers;

use App\Models\Taller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TallerController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');
        $usuario = Auth::user();

        // --- LÓGICA DE PERIODO DE INSCRIPCIÓN ---
        // Inscripciones abiertas solo en: Enero (1), Mayo (5), Septiembre (9)
        $mesActual = Carbon::now('America/Mexico_City')->month;
        $mesesInscripcion = [1, 4, 5, 9]; // Dejé el 4 para tus pruebas actuales, Bianca.
        $periodoAbierto = in_array($mesActual, $mesesInscripcion);

        $talleres = Taller::withCount('inscripciones')
            ->with(['inscripciones'])
            ->when($buscar, function ($query, $buscar) {
                return $query->where('nombre', 'LIKE', '%' . $buscar . '%')
                             ->orWhere('detalle', 'LIKE', '%' . $buscar . '%');
            })->get();

        if ($usuario->ID_rol == 1) {
            return view('estudiantes.explorar', compact('talleres', 'buscar', 'periodoAbierto'));
        }

        $totalTalleres = Taller::count();
        $totalEstudiantes = Usuario::where('ID_rol', 1)->count(); 
        $totalInstructores = Usuario::where('ID_rol', 2)->count();

        return view('talleres.index', compact(
            'talleres', 
            'totalTalleres', 
            'totalEstudiantes', 
            'totalInstructores', 
            'buscar'
        )); 
    }

    public function create()
    {
        return view('talleres.create'); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'  => 'required|string|max:255|unique:talleres,nombre',
            'detalle' => 'required|string',
            'activo'  => 'required|boolean',
            'cupo'    => 'required|integer|min:1',
            'horario' => 'nullable|string'
        ]);

        Taller::create($request->all()); 

        return redirect()->route('talleres.index')->with('exito', 'Taller registrado correctamente.'); 
    }

    public function edit($id)
    {
        $taller = Taller::findOrFail($id); 
        return view('talleres.edit', compact('taller')); 
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'  => 'required|string|max:255|unique:talleres,nombre,'.$id.',ID_taller',
            'detalle' => 'required|string',
            'activo'  => 'required|boolean',
            'cupo'    => 'required|integer|min:1',
            'horario' => 'nullable|string'
        ]);

        $taller = Taller::findOrFail($id);
        $taller->update($request->all()); 

        return redirect()->route('talleres.index')->with('exito', 'El taller se ha actualizado correctamente.'); 
    }

    public function destroy($id)
    {
        $taller = Taller::findOrFail($id);
        try {
            $taller->delete(); 
            return redirect()->route('talleres.index')->with('exito', 'Taller eliminado correctamente.'); 
        } catch (\Exception $e) {
            return redirect()->route('talleres.index')->with('error', 'No se puede eliminar: tiene registros vinculados.');
        }
    }

    public function indexQR()
    {
        $talleres = DB::table('talleres as t')
            ->leftJoin('imparte_taller as it', 't.ID_taller', '=', 'it.ID_taller')
            ->leftJoin('usuarios as u', 'it.ID_usuario', '=', 'u.ID_usuario')
            ->select('t.*', 'u.nombre as maestro_nombre')
            ->where('t.activo', 1)
            ->get();
        
        return view('talleres.index_qr', compact('talleres'));
    }
}
