<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Taller extends Model
{
    use HasFactory;

    protected $table = 'talleres';
    protected $primaryKey = 'ID_taller';
    public $timestamps = false;

    // IMPLEMENTACIÓN CORREGIDA: Se agregaron 'horario' y 'periodo' para que Laravel permita guardarlos en la BD
    protected $fillable = [
        'nombre', 
        'detalle', 
        'activo', 
        'cupo', 
        'horario', 
        'periodo'
    ];

    // Relación con los instructores
    public function imparticiones()
    {
        return $this->hasMany(ImparteTaller::class, 'ID_taller', 'ID_taller');
    }

    // NUEVA RELACIÓN: Para que el controlador pueda contar los alumnos
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'ID_taller', 'ID_taller');
    }
}
