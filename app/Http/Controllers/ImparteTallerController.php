<?php

namespace App\Http\Controllers;

use App\Models\ImparteTaller;
use App\Models\Usuario;
use App\Models\Taller;
use Illuminate\Http\Request;

class ImparteTallerController extends Controller
{
    public function index()
    {
        $imparticiones = ImparteTaller::with(['usuario', 'taller'])->get();
        return view('imparte_taller.index', compact('imparticiones'));
    }

    public function create()
    {
        // FILTRADO ESTRICTO: Solo usuarios activos con Rol de Instructor (Rol 2)
        $usuarios = Usuario::where('activo', 1)
                           ->where('ID_rol', 2) 
                           ->get();

        $talleres = Taller::where('activo', 1)->get();
        
        return view('imparte_taller.create', compact('usuarios', 'talleres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_usuario' => 'required|exists:usuarios,ID_usuario',
            'ID_taller' => 'required|exists:talleres,ID_taller',
            'periodo' => 'required|string|max:50',
            'fecha' => 'required|date',
            'activo' => 'required|in:0,1'
        ], [
            'ID_usuario.required' => 'Debes seleccionar un instructor válido.',
            'ID_taller.required' => 'Debes seleccionar un taller.'
        ]);

        ImparteTaller::create($request->all());

        return redirect()->route('imparte_taller.index')
            ->with('exito', 'Asignación de taller creada correctamente.');
    }

    public function edit($id)
    {
        $imparte = ImparteTaller::findOrFail($id);
        
        // Mantenemos el filtro para asegurar que no se asignen estudiantes al editar
        $usuarios = Usuario::where('ID_rol', 2)->get();
        $talleres = Taller::all();
        
        return view('imparte_taller.edit', compact('imparte', 'usuarios', 'talleres'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ID_usuario' => 'required|exists:usuarios,ID_usuario',
            'ID_taller' => 'required|exists:talleres,ID_taller',
            'periodo' => 'required|string|max:50',
            'fecha' => 'required|date',
            'activo' => 'required|in:0,1'
        ]);

        $imparte = ImparteTaller::findOrFail($id);
        $imparte->update($request->all());

        return redirect()->route('imparte_taller.index')
            ->with('exito', 'Asignación actualizada correctamente.');
    }

    public function destroy($id)
    {
        $imparte = ImparteTaller::findOrFail($id);
        
        try {
            $imparte->delete();
            return redirect()->route('imparte_taller.index')
                ->with('exito', 'Asignación eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('imparte_taller.index')
                ->with('error', 'No se puede eliminar: Esta asignación tiene registros vinculados.');
        }
    }
}