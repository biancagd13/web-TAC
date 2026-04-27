<?php

namespace App\Http\Controllers;

use App\Models\Taller;
use App\Models\Usuario;
use Illuminate\Http\Request;

class InicioController extends Controller
{
    public function index()
    {
        // Estadísticas reales para el Dashboard (Mockup Pág 3)
        $totalTalleres = Taller::count();
        $totalEstudiantes = Usuario::where('ID_rol', 1)->count();
        $totalInstructores = Usuario::where('ID_rol', 2)->count();

        // Si tienes una vista específica para el inicio
        return view('inicio.index', compact('totalTalleres', 'totalEstudiantes', 'totalInstructores'));
    }
}