<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\DetalleAsistencia;
use App\Models\Inscripcion;
use App\Models\ImparteTaller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Exports\AsistenciasExport;
use Maatwebsite\Excel\Facades\Excel;

class AsistenciaController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $query = Asistencia::with('imparteTaller.usuario', 'imparteTaller.taller');
        
        if ($usuario->ID_rol != 3) {
            $query->whereHas('imparteTaller', function($q) use ($usuario) {
                $q->where('ID_usuario', $usuario->ID_usuario);
            });
        }

        $asistencias = $query->orderBy('fecha_creacion', 'desc')->get();
        return view('asistencias.index', compact('asistencias'));
    }

    public function create()
    {
        $usuario = Auth::user();
        $query = ImparteTaller::with(['usuario', 'taller'])->where('activo', 1);
        
        if ($usuario->ID_rol != 3) {
            $query->where('ID_usuario', $usuario->ID_usuario);
        }
        
        $imparticiones = $query->get();
        return view('asistencias.create', compact('imparticiones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_imparte' => 'required|exists:imparte_taller,ID_imparte'
        ]);

        $ultimaSesion = Asistencia::where('ID_imparte', $request->ID_imparte)
            ->where('fecha_creacion', '>=', Carbon::now()->subHours(12))
            ->first();

        if ($ultimaSesion) {
            $tiempoRestante = Carbon::parse($ultimaSesion->fecha_creacion)
                ->addHours(12)
                ->diffForHumans(['parts' => 2]);

            return redirect()->back()->with('error', 
                "Ya existe una sesión activa para este taller (Sesión #{$ultimaSesion->ID_asistencia}). " .
                "Podrás generar una nueva en: " . $tiempoRestante
            );
        }

        $asistencia = Asistencia::create([
            'ID_imparte' => $request->ID_imparte,
            'ID_usuario' => Auth::user()->ID_usuario 
        ]);

        return redirect()->route('asistencias.index')->with('exito', 'Sesión habilitada correctamente.');
    }

    public function listaManual($id)
    {
        $asistencia = Asistencia::with('imparteTaller.taller')->findOrFail($id);
        $alumnos = Inscripcion::where('ID_taller', $asistencia->imparteTaller->ID_taller)->with('usuario', 'carrera')->get();
        $presentesIds = DetalleAsistencia::where('ID_asistencia', $id)->pluck('ID_usuario')->toArray();

        return view('asistencias.lista_manual', compact('asistencia', 'alumnos', 'presentesIds'));
    }

    public function guardarListaManual(Request $request, $id)
    {
        DetalleAsistencia::where('ID_asistencia', $id)->delete();
        if ($request->has('alumnos_presentes')) {
            foreach ($request->alumnos_presentes as $idUsuario) {
                DetalleAsistencia::create([
                    'ID_asistencia' => $id,
                    'ID_usuario' => $idUsuario,
                    'estatus' => 'Presente'
                ]);
            }
        }
        return redirect()->route('asistencias.index')->with('exito', 'Lista actualizada correctamente.');
    }

    public function show($id)
    {
        $asistencia = Asistencia::with('imparteTaller.usuario', 'imparteTaller.taller')->findOrFail($id);
        
        if (Carbon::parse($asistencia->fecha_creacion)->addHours(12)->isPast()) {
            return redirect()->route('asistencias.index')->with('error', 'Este código QR ha expirado (Límite 12 horas).');
        }

        $t = time();
        $urlEscaneo = route('detalle_asistencias.create', [
            'id_asistencia' => $id,
            't' => $t,
            'token' => md5($t . $id)
        ]);

        $codigoQR = QrCode::size(300)->color(30, 109, 42)->margin(1)->generate($urlEscaneo);
        return view('asistencias.show', compact('asistencia', 'codigoQR'));
    }

    public function reporteMensual(Request $request)
{
    $request->validate(['ID_imparte' => 'required']);
    $imparte = ImparteTaller::with(['taller', 'usuario'])->findOrFail($request->ID_imparte);
    $alumnos = Inscripcion::where('ID_taller', $imparte->ID_taller)->with('usuario')->get();
    
    $query = Asistencia::where('ID_imparte', $request->ID_imparte)->orderBy('fecha_creacion', 'asc');

    $tipo = $request->tipo_reporte;
    $columnas = [];
    $tituloFecha = "";
    $mes = $request->mes ?? date('m'); // Definimos $mes por defecto para evitar el error

    if ($tipo == 'mensual') {
        $mes = (int) $request->mes;
        $query->whereMonth('fecha_creacion', $mes);
        $sesiones = $query->get();
        
        foreach($sesiones as $s) {
            $columnas[] = [
                'id' => $s->ID_asistencia,
                'label' => date('d/m', strtotime($s->fecha_creacion))
            ];
        }
        $tituloFecha = "Mes: " . Carbon::create(null, $mes)->translatedFormat('F');
    } else {
        $mesesIds = explode(',', $request->periodo);
        $query->whereIn(\DB::raw('MONTH(fecha_creacion)'), $mesesIds);
        
        foreach($mesesIds as $mId) {
            $columnas[] = [
                'id' => (int)$mId,
                'label' => Carbon::create(null, (int)$mId)->translatedFormat('F'),
                'es_mes' => true
            ];
        }
        $tituloFecha = "Periodo: " . $request->periodo_nombre;
    }

    $sesionesMes = $query->get(); // Mantenemos el nombre para no romper la vista

    $pdf = Pdf::loadView('asistencias.reporte_mensual', compact('imparte', 'alumnos', 'sesionesMes', 'mes', 'tituloFecha', 'tipo', 'columnas'));
    return $pdf->setPaper('letter', 'landscape')->download('Reporte_TAC_'.str_replace(' ', '_', $tituloFecha).'.pdf');
}

    public function exportarExcelMensual(Request $request)
{
    $request->validate(['ID_imparte' => 'required']);
    $imparte = ImparteTaller::with(['taller', 'usuario'])->findOrFail($request->ID_imparte);
    $alumnos = Inscripcion::where('ID_taller', $imparte->ID_taller)->with('usuario')->get();
    
    $tipo = $request->tipo_reporte;
    $dataExcel = [];
    $nombreFecha = "";

    if ($tipo == 'mensual') {
        $mes = (int) $request->mes;
        $nombreFecha = "Mes de " . Carbon::create(null, $mes)->translatedFormat('F');
        $sesiones = Asistencia::where('ID_imparte', $request->ID_imparte)
                    ->whereMonth('fecha_creacion', $mes)
                    ->orderBy('fecha_creacion', 'asc')->get();

        foreach ($alumnos as $al) {
            $fila = ['alumno' => $al->usuario->nombre];
            $totalInd = 0;
            foreach ($sesiones as $s) {
                $presente = DetalleAsistencia::where('ID_asistencia', $s->ID_asistencia)
                                            ->where('ID_usuario', $al->ID_usuario)->exists();
                $fila[date('d/m', strtotime($s->fecha_creacion))] = $presente ? 'X' : '-';
                if($presente) $totalInd++;
            }
            $fila['TOTAL'] = $totalInd;
            $dataExcel[] = $fila;
        }
    } else {
        // --- REPORTE CUATRIMESTRAL PRO ---
        $mesesIds = explode(',', $request->periodo);
        $nombreFecha = "Periodo: " . $request->periodo_nombre;

        foreach ($alumnos as $al) {
            $fila = ['alumno' => $al->usuario->nombre];
            $totalCuatri = 0;
            foreach ($mesesIds as $mId) {
                $nombreMesCol = Carbon::create(null, (int)$mId)->translatedFormat('F');
                // Contamos asistencias totales del alumno en ese mes y taller
                $conteoMes = DetalleAsistencia::whereHas('asistencia', function($q) use ($mId, $imparte) {
                                $q->where('ID_imparte', $imparte->ID_imparte)
                                  ->whereMonth('fecha_creacion', (int)$mId);
                            })->where('ID_usuario', $al->ID_usuario)->count();
                
                $fila[strtoupper($nombreMesCol)] = $conteoMes;
                $totalCuatri += $conteoMes;
            }
            $fila['TOTAL CUATRI'] = $totalCuatri;
            $dataExcel[] = $fila;
        }
    }

    $titulo = "Taller: {$imparte->taller->nombre} | {$nombreFecha} | Instructor: {$imparte->usuario->nombre}";
    return Excel::download(new AsistenciasExport($dataExcel, $titulo), 'Concentrado_Asistencias_TAC.xlsx');
}

    public function destroy($id)
    {
        $asistencia = Asistencia::findOrFail($id);
        $asistencia->delete();
        return redirect()->route('asistencias.index')->with('exito', 'Sesión eliminada.');
    }
}
