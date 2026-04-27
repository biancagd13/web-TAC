<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencias'; //
    protected $primaryKey = 'ID_asistencia'; //
    public $timestamps = false;

    // Solo estas columnas existen en tu migración
    protected $fillable = ['ID_imparte']; 

    // Relación con la tabla ImparteTaller
    public function imparteTaller() {
    return $this->belongsTo(ImparteTaller::class, 'ID_imparte', 'ID_imparte');
}
}