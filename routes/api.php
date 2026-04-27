<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AsistenciaController;
use App\Http\Controllers\Api\TallerController;
use App\Http\Controllers\Api\AvisoController;
use App\Http\Controllers\Api\ConstanciaController;
use App\Http\Controllers\UsuarioController; // Importado para la foto

// --- RUTAS PÚBLICAS (PARA DESCARGAS DESDE NAVEGADOR) ---
Route::get('/asistencia/concentrado/{id_imparte}', [AsistenciaController::class, 'generarConcentrado']);
Route::post('/login', [AuthController::class, 'login']);

// --- RUTAS PROTEGIDAS ---
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/user/profile', [AuthController::class, 'profile']);

    // NUEVA RUTA API PARA FOTO
    Route::post('/user/update-foto', [UsuarioController::class, 'updateFoto']);

    Route::get('/mis-asistencias', [TallerController::class, 'misTalleres']); 
    Route::post('/asistencia/registrar', [AsistenciaController::class, 'registrar']);
    Route::post('/asistencia/manual', [AsistenciaController::class, 'paseManual']);
    Route::get('/alumno/avisos', [AvisoController::class, 'getAvisosAlumno']);
    Route::get('/alumno/constancias', [ConstanciaController::class, 'getConstanciasAlumno']);
    Route::get('/alumno/constancias/{id}/download', [ConstanciaController::class, 'downloadPDF']);
    Route::get('/talleres/disponibles', [TallerController::class, 'getTalleresDisponibles']);
    Route::post('/talleres/inscribir', [TallerController::class, 'inscribirAlumno']);
    Route::post('/asistencia/abrir-sesion', [AsistenciaController::class, 'abrirSesion']);
    Route::get('/maestro/estadisticas', [TallerController::class, 'getEstadisticasMaestro']);
    Route::get('/asistencia/estadisticas/{id_imparte}', [AsistenciaController::class, 'obtenerEstadisticas']);
    Route::get('/asistencia/lista/{id}', [AsistenciaController::class, 'listaAlumnos']);
    Route::post('/avisos', [AvisoController::class, 'store']); 
    Route::get('/avisos', [AvisoController::class, 'index']); 
    Route::post('/taller/validar-qr', [TallerController::class, 'validarQR']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/talleres/inscribir-qr', [TallerController::class, 'inscribirPorQR']);

});