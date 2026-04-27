<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use Illuminate\Http\Request;

class CarreraController extends Controller
{
    public function index()
    {
        $carreras = Carrera::all();
        return view('carreras.index', compact('carreras'));  
    }

    public function create()
    {
        return view('carreras.create'); 
    }

    public function store(Request $request)
    {
        // Validación Senior: Clave única para evitar duplicados (ej. no dos DSM-53)
        $request->validate([
            'nombre' => 'required|string|max:100',
            'clave'  => 'required|string|max:20|unique:carreras,clave',
            'detalle'=> 'nullable|string',
            'activo' => 'required|boolean'
        ], [
            'nombre.required' => 'El nombre de la carrera es obligatorio.',
            'clave.required' => 'La clave de la carrera es necesaria.',
            'clave.unique' => 'Esta clave ya ha sido registrada para otra carrera.'
        ]);

        Carrera::create($request->only([
            'nombre', 'clave', 'detalle', 'activo',
        ]));

        return redirect()->route('carreras.index')
            ->with('exito', 'La carrera se ha registrado correctamente en el sistema.');  
    }

    public function edit(Carrera $carrera)
    {
        return view('carreras.edit', compact('carrera')); 
    }

    public function update(Request $request, Carrera $carrera)
    {
        // Validamos permitiendo que la clave sea la misma de esta carrera pero no de otras
        $request->validate([
            'nombre' => 'required|string|max:100',
            'clave'  => 'required|string|max:20|unique:carreras,clave,' . $carrera->ID_carrera . ',ID_carrera',
            'detalle'=> 'nullable|string',
            'activo' => 'required|boolean'
        ], [
            'nombre.required' => 'El campo nombre no puede quedar vacío.',
            'clave.unique' => 'Esa clave ya está en uso por otra carrera.'
        ]);

        $carrera->update($request->only([
            'nombre', 'clave', 'detalle', 'activo',
        ]));  

        return redirect()->route('carreras.index')
            ->with('exito', 'Los datos de la carrera se han actualizado con éxito.');  
    }

    public function destroy(Carrera $carrera)
    {
        try {
            $carrera->delete();
            return redirect()->route('carreras.index')
                ->with('exito', 'Carrera eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('carreras.index')
                ->with('error', 'No se puede eliminar la carrera porque tiene inscripciones asociadas.');
        }
    }
}