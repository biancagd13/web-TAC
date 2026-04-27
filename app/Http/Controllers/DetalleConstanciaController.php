<?php

namespace App\Http\Controllers;

use App\Models\DetalleConstancia;
use App\Models\Constancia;
use Illuminate\Http\Request;

class DetalleConstanciaController extends Controller
{
    /**
     * Muestra la bitácora de validaciones para el Administrador (Alexa).
     */
    public function index()
    {
        $detalles = DetalleConstancia::with('constancia.usuario', 'constancia.imparteTaller.taller')->get();
        return view('detalle_constancias.index', compact('detalles'));
    }

    /**
     * Muestra el formulario de creación cargando las constancias disponibles en la UTVT.
     */
    public function create()
    {
        $constancias = Constancia::with(['usuario', 'imparteTaller.taller'])->get();
        return view('detalle_constancias.create', compact('constancias'));
    }

    /**
     * Almacena el detalle aplicando validaciones estrictas.
     */
    public function store(Request $request)
    {
        // Forzamos que todos los campos sean 'required' para evitar inconsistencias.
        $request->validate([
            'ID_constancia' => 'required|exists:constancias,ID_constancia',
            'codigo_validacion' => 'required|string|max:100|unique:detalle_constancias,codigo_validacion',
            'firma_digital' => 'required|string',
            'fecha_envio_email' => 'required|date'
        ], [
            'codigo_validacion.required' => 'Error: El código de validación es obligatorio.',
            'codigo_validacion.unique' => 'Error: Este código ya existe en el sistema.',
            'firma_digital.required' => 'Error: La firma digital es obligatoria.',
            'fecha_envio_email.required' => 'Error: Debes indicar la fecha de envío.'
        ]);

        DetalleConstancia::create($request->only([
            'codigo_validacion', 
            'firma_digital', 
            'fecha_envio_email', 
            'ID_constancia'
        ]));

        return redirect()->route('detalle_constancias.index')
                         ->with('exito', 'Validación registrada con éxito en el SISTEMA TAC.');
    }

    /**
     * Carga los datos para editar una validación específica.
     */
    public function edit($id)
    {
        $detalle = DetalleConstancia::findOrFail($id);
        $constancias = Constancia::with(['usuario', 'imparteTaller.taller'])->get();
        return view('detalle_constancias.edit', compact('detalle', 'constancias'));
    }

    /**
     * Actualiza el detalle permitiendo mantener el mismo código si no cambia.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'ID_constancia' => 'required|exists:constancias,ID_constancia',
            'codigo_validacion' => 'required|string|max:100|unique:detalle_constancias,codigo_validacion,' . $id . ',ID_detalle_constancia',
            'firma_digital' => 'required|string',
            'fecha_envio_email' => 'required|date'
        ]);

        $detalle = DetalleConstancia::findOrFail($id);
        $detalle->update($request->only([
            'codigo_validacion', 
            'firma_digital', 
            'fecha_envio_email', 
            'ID_constancia'
        ]));

        return redirect()->route('detalle_constancias.index')
                         ->with('exito', 'Validación actualizada con éxito.');
    }

    /**
     * Elimina el registro de validación.
     */
    public function destroy($id)
    {
        $detalle = DetalleConstancia::findOrFail($id);
        $detalle->delete();
        return redirect()->route('detalle_constancias.index')
                         ->with('exito', 'Detalle de validación eliminado correctamente.');
    }
}