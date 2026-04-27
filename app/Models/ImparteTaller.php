<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImparteTaller extends Model
{
    use HasFactory;

    protected $table = 'imparte_taller';
    protected $primaryKey = 'ID_imparte';
    public $timestamps = false;

    protected $fillable = [
        'ID_usuario',
        'ID_taller',
        'periodo',
        'fecha',
        'activo'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ID_usuario', 'ID_usuario');
    }

    public function taller()
    {
        return $this->belongsTo(Taller::class, 'ID_taller', 'ID_taller');
    }
}