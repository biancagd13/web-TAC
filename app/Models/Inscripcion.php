<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripcion'; 
    // Usamos la tilde porque así la definiste en la migración
    protected $primaryKey = 'ID_inscripción'; 
    public $timestamps = false;

    protected $fillable = ['periodo', 'fecha', 'ID_carrera', 'ID_usuario', 'ID_taller'];

    public function usuario() { return $this->belongsTo(Usuario::class, 'ID_usuario', 'ID_usuario'); }
    public function carrera() { return $this->belongsTo(Carrera::class, 'ID_carrera', 'ID_carrera'); }
    public function taller()  { return $this->belongsTo(Taller::class, 'ID_taller', 'ID_taller'); }
}