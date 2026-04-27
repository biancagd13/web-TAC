<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Aviso extends Model
{
    use HasFactory;

    protected $table = 'avisos';
    protected $primaryKey = 'ID_aviso'; 
    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'contenido',
        'fecha_publicacion',
        'ID_usuario',
        'ID_taller' // <--- IMPORTANTE: Agregamos esto aquí
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ID_usuario', 'ID_usuario');
    }

    // Definimos la relación con Taller aunque no esté la llave física en la DB
    public function taller()
    {
        return $this->belongsTo(Taller::class, 'ID_taller', 'ID_taller');
    }
}
