<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetalleAsistencia extends Model
{
    use HasFactory;

    protected $table = 'detalle_asistencias'; // Nombre exacto de tu migración
    protected $primaryKey = 'ID_detalle_asistencia'; // Llave exacta
    public $timestamps = false;

    // Campos reales de tu migración
    protected $fillable = [ 
        'fecha',
        'entro',
        'ID_asistencia',
        'ID_usuario'
    ];

    // Relaciones para mostrar datos en el index
    public function usuario() { 
        return $this->belongsTo(Usuario::class, 'ID_usuario', 'ID_usuario'); 
    }
    public function asistencia() { 
        return $this->belongsTo(Asistencia::class, 'ID_asistencia', 'ID_asistencia'); 
    }
}