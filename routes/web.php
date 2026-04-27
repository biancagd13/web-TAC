<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TallerController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\AvisoController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\ConstanciaController;
use App\Http\Controllers\DetalleAsistenciaController;
use App\Http\Controllers\DetalleConstanciaController;
use Illuminate\Support\Facades\Auth;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    Route::get('/home', function () {
        return redirect('/');
    });

    Route::get('/', function () {
        $rol = Auth::user()->ID_rol;
        if ($rol == 3 || $rol == 1) {
            return redirect()->route('talleres.index');
        } elseif ($rol == 2) {
            return redirect()->route('perfil');
        }
        return redirect()->route('perfil');
    })->name('inicio');

    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil');

    // NUEVA RUTA FOTO PERFIL
    Route::post('/perfil/update-foto', [UsuarioController::class, 'updateFoto'])->name('usuarios.updateFoto');

    Route::middleware(['rol:3'])->group(function () {
        Route::resource('usuarios', UsuarioController::class);
        Route::resource('roles', App\Http\Controllers\RolController::class);
        Route::resource('carreras', App\Http\Controllers\CarreraController::class);
        Route::resource('imparte_taller', App\Http\Controllers\ImparteTallerController::class);
        Route::get('/talleres-qr-imprimibles', [TallerController::class, 'indexQR'])->name('talleres.qr');
    });

    Route::middleware(['rol:1'])->group(function () {
        Route::get('/mis-talleres', function() {
            return redirect()->route('talleres.index');
        })->name('estudiante.talleres');
    });

    Route::get('/liberacion-constancias', [ConstanciaController::class, 'index'])->name('constancias.maestro');
    Route::post('/liberar-constancia', [ConstanciaController::class, 'liberar'])->name('constancias.liberar');

    Route::resource('talleres', TallerController::class);
    Route::resource('avisos', AvisoController::class);
    Route::resource('inscripciones', InscripcionController::class);
    Route::resource('asistencias', AsistenciaController::class);
    Route::resource('constancias', ConstanciaController::class);
    Route::resource('detalle_asistencias', DetalleAsistenciaController::class);
    Route::resource('detalle_constancias', DetalleConstanciaController::class);

    Route::get('/descargar-constancia/{id}', [ConstanciaController::class, 'descargarPDF'])->name('constancias.descargar');
    Route::get('/asistencias-reporte', [AsistenciaController::class, 'reporteMensual'])->name('asistencias.reporte');
    Route::get('/asistencias-excel', [AsistenciaController::class, 'exportarExcelMensual'])->name('asistencias.excel');
    Route::get('/asistencias/{id}/lista-manual', [AsistenciaController::class, 'listaManual'])->name('asistencias.lista_manual');
    Route::post('/asistencias/{id}/guardar-manual', [AsistenciaController::class, 'guardarListaManual'])->name('asistencias.guardar_manual');

    Route::resource('asistencias', AsistenciaController::class)->except(['edit', 'update']); 
});