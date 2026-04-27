<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetalleConstancia extends Model
{
    use HasFactory;

    protected $table = 'detalle_constancias'; 
    protected $primaryKey = 'ID_detalle_constancia'; 
    public $timestamps = false;

    protected $fillable = [
        'codigo_validacion',
        'firma_digital',
        'fecha_envio_email',
        'ID_constancia'
    ];

    // Relación para saber a qué constancia pertenece este detalle
    public function constancia()
    {
        return $this->belongsTo(Constancia::class, 'ID_constancia', 'ID_constancia');
    }
}