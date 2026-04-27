<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Implementación para API

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable; // Añadido HasApiTokens

    protected $table = 'usuarios';
    protected $primaryKey = 'ID_usuario';
    public $timestamps = false;

    protected $fillable = ['nombre', 'correo', 'password','telefono','foto_perfil', 'ID_rol', 'activo'];
    protected $hidden = ['password'];

    /**
     * RELACIONES EXISTENTES (No se tocan)
     */
    public function rol() {
        return $this->belongsTo(Rol::class, 'ID_rol', 'ID_rol');
    }

    public function inscripciones() {
        return $this->hasMany(Inscripcion::class, 'ID_usuario', 'ID_usuario');
    }

    /**
     * RELACIÓN PARA EL PERFIL DEL ESTUDIANTE
     */
    public function detalleAsistencias() {
        return $this->hasMany(DetalleAsistencia::class, 'ID_usuario', 'ID_usuario');
    }

    /**
     * RELACIÓN PARA EL CÁLCULO DEL 80%
     */
    public function asistencias() {
        return $this->hasMany(DetalleAsistencia::class, 'ID_usuario', 'ID_usuario');
    }

    /* |--------------------------------------------------------------------------
    | NUEVAS IMPLEMENTACIONES PARA LA API (SISTEMA TAC)
    |--------------------------------------------------------------------------
    | Estas funciones ayudan a que la App de React Native entienda mejor 
    | los roles de Bianca (1) y Pedro López (2).
    */

    /**
     * Determina si el usuario es Estudiante (Bianca)
     */
    public function esEstudiante()
    {
        return $this->ID_rol === 1;
    }

    /**
     * Determina si el usuario es Instructor (Pedro)
     */
    public function esInstructor()
    {
        return $this->ID_rol === 2;
    }

    /**
     * Relación con los Avisos (para que Bianca vea noticias en la App)
     */
    public function avisos() {
        return $this->hasMany(Aviso::class, 'ID_usuario', 'ID_usuario');
    }
}
