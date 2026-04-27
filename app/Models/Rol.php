<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'rol';
    protected $primaryKey = 'ID_rol';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'ID_rol', 'ID_rol');
    }
}