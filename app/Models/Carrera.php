<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Carrera extends Model
{
    use HasFactory;

    protected $table = 'carreras';
    protected $primaryKey = 'ID_carrera';
    public $timestamps = false;

    protected $fillable = ['nombre', 'clave', 'detalle', 'activo'];

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'ID_carrera', 'ID_carrera');
    }
}