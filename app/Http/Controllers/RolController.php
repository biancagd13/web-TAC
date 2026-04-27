<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function index()
    {
        $roles = Rol::all();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        // Validación Senior: El nombre es obligatorio y no debe repetirse
        $request->validate([
            'nombre' => 'required|string|max:50|unique:rol,nombre'
        ], [
            'nombre.required' => 'El nombre del rol es obligatorio.',
            'nombre.unique' => 'Este rol ya existe en el sistema.'
        ]);

        Rol::create($request->all());

        return redirect()->route('roles.index')
            ->with('exito', 'El nuevo rol se ha registrado correctamente.');
    }

    public function edit($id)
    {
        $rol = Rol::findOrFail($id);
        return view('roles.edit', compact('rol'));
    }

    public function update(Request $request, $id)
    {
        // Al actualizar, ignoramos el nombre del rol actual para que no marque error de "ya existe"
        $request->validate([
            'nombre' => 'required|string|max:50|unique:rol,nombre,'.$id.',ID_rol'
        ], [
            'nombre.required' => 'El nombre del rol no puede estar vacío.',
            'nombre.unique' => 'Ese nombre de rol ya está en uso.'
        ]);

        $rol = Rol::findOrFail($id);
        $rol->update($request->all());

        return redirect()->route('roles.index')
            ->with('exito', 'Rol actualizado con éxito.');
    }

    public function destroy($id)
    {
        $rol = Rol::findOrFail($id);
        
        try {
            $rol->delete();
            return redirect()->route('roles.index')
                ->with('exito', 'Rol eliminado correctamente.');
        } catch (\Exception $e) {
            // Si el rol tiene usuarios, Laravel lanzará error de integridad y caerá aquí
            return redirect()->route('roles.index')
                ->with('error', 'Acción bloqueada: No puedes eliminar este rol porque tiene usuarios asignados.');
        }
    }
}