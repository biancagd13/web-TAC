<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;

class RolTableSeeder extends Seeder
{
    public function run(): void
    {
        // Asegúrate de que los nombres de las columnas coincidan con tu migración
        Rol::create([
            'ID_rol' => 1,
            'nombre' => 'Estudiante' // Cambié 'nombre_rol' por 'nombre'
        ]);

        Rol::create([
            'ID_rol' => 2,
            'nombre' => 'Instructor'
        ]);

        Rol::create([
            'ID_rol' => 3,
            'nombre' => 'Administrador'
        ]);
    }
}