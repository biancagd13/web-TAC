<?php

namespace App\Http\Controllers;

use App\Models\DetalleAsistencia;
use App\Models\Asistencia;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class DetalleAsistenciaController extends Controller
{
    public function index()
    {
        $detalles = DetalleAsistencia::with(['usuario', 'asistencia.imparteTaller.taller'])->get();
        return view('detalle_asistencias.index', compact('detalles'));
    }

    /**
     * MÉTODO MEJORADO: Ahora soporta el registro manual y el automático por QR con validación de tiempo
     */
    public function create(Request $request)
    {
        // 1. Verificamos si el alumno viene desde un escaneo de QR y capturamos el tiempo
        $id_asistencia_qr = $request->query('id_asistencia');
        $timestamp_qr = $request->query('t'); 

        // 2. Si hay un ID de asistencia por QR, registramos automáticamente validando la expiración
        if ($id_asistencia_qr) {
            return $this->registrarPorQR($id_asistencia_qr, $timestamp_qr);
        }

        // 3. Si no es por QR, cargamos la lógica manual original
        $asistencias = Asistencia::with('imparteTaller.taller')->get();
        
        $rolEstudiante = Rol::where('nombre', 'like', '%Estudiante%')
                            ->orWhere('nombre', 'like', '%Alumno%')
                            ->first();

        $usuarios = Usuario::where('ID_rol', $rolEstudiante->ID_rol)
                           ->where('activo', 1)
                           ->get();

        return view('detalle_asistencias.create', compact('asistencias', 'usuarios'));
    }

    /**
     * Lógica Senior: Procesa el escaneo del alumno validando que el QR sea reciente
     * AJUSTADO: Ahora guarda '1' en 'entro' para el estatus Sí/No
     */
    private function registrarPorQR($id_asistencia, $timestamp_qr)
    {
        $id_usuario = Auth::user()->ID_usuario; 
        $tiempo_actual = time(); 

        // VALIDACIÓN DE SEGURIDAD: Si el QR tiene más de 120 segundos, se rechaza
        if ($timestamp_qr && ($tiempo_actual - $timestamp_qr) > 120) {
            return redirect()->route('inicio')
                ->with('error', 'El código QR ha expirado. Por favor, escanea el código actualizado en la pantalla del instructor.');
        }

        // Verificamos si ya registró su asistencia hoy para evitar duplicados
        $existe = DetalleAsistencia::where('ID_asistencia', $id_asistencia)
                                   ->where('ID_usuario', $id_usuario)
                                   ->first();

        if ($existe) {
            return redirect()->route('inicio')->with('error', 'Ya has registrado tu asistencia en esta sesión.');
        }

        // NUEVA IMPLEMENTACIÓN: Se guarda 1 (booleano) para que el Admin vea "Sí"
        DetalleAsistencia::create([
            'fecha'         => now()->format('Y-m-d'),
            'entro'         => 1, // <--- Valor booleano para estatus
            'ID_asistencia' => $id_asistencia,
            'ID_usuario'    => $id_usuario
        ]);

        return redirect()->route('inicio')->with('exito', '¡Asistencia registrada exitosamente mediante QR!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha'         => 'required|date',
            'ID_asistencia' => 'required|exists:asistencias,ID_asistencia',
            'ID_usuario'    => 'required|exists:usuarios,ID_usuario'
        ], [
            'ID_usuario.required'    => 'Debe seleccionar un alumno activo.',
            'ID_asistencia.required' => 'Debe vincular una sesión de asistencia.'
        ]);

        // Aseguramos que el registro manual también guarde el '1' en la columna 'entro'
        $data = $request->only(['fecha', 'ID_asistencia', 'ID_usuario']);
        $data['entro'] = 1; 

        DetalleAsistencia::create($data);

        return redirect()->route('detalle_as_asistencias.index')
            ->with('exito', 'Detalle de asistencia registrado correctamente');
    }

    public function edit($id)
    {
        $detalle = DetalleAsistencia::findOrFail($id);
        $asistencias = Asistencia::all();
        
        $rolEstudiante = Rol::where('nombre', 'like', '%Estudiante%')
                            ->orWhere('nombre', 'like', '%Alumno%')
                            ->first();
                            
        $usuarios = Usuario::where('ID_rol', $rolEstudiante->ID_rol)->get();

        return view('detalle_asistencias.edit', compact('detalle', 'asistencias', 'usuarios'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fecha'         => 'required|date',
            'ID_asistencia' => 'required',
            'ID_usuario'    => 'required'
        ]);

        $detalle = DetalleAsistencia::findOrFail($id);
        
        $detalle->update($request->only([
            'fecha', 'entro', 'ID_asistencia', 'ID_usuario'
        ]));

        return redirect()->route('detalle_asistencias.index')
            ->with('exito', 'Detalle actualizado correctamente');
    }

    public function destroy($id)
    {
        $detalle = DetalleAsistencia::findOrFail($id);
        $detalle->delete();

        return redirect()->route('detalle_asistencias.index')
            ->with('exito', 'Detalle eliminado correctamente');
    }
}