<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Constancia extends Model
{
    use HasFactory;

    protected $table = 'constancias';
    protected $primaryKey = 'ID_constancia';
    public $timestamps = false;

    protected $fillable = [
        'fecha_emision',
        'ID_usuario',
        'ID_imparte'
    ];

    // Relación con el alumno
    public function usuario() { 
        return $this->belongsTo(Usuario::class, 'ID_usuario', 'ID_usuario'); 
    }

    // Relación con el taller impartido
    public function imparteTaller() { 
        return $this->belongsTo(ImparteTaller::class, 'ID_imparte', 'ID_imparte'); 
    }

    /**
     * IMPLEMENTACIÓN FALTANTE: Conexión con el detalle de validación
     */
    public function detalleConstancia() {
        return $this->hasOne(DetalleConstancia::class, 'ID_constancia', 'ID_constancia');
    }
}