<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuariosTableSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ADMINISTRADOR (Rol 3)
        Usuario::create([
            'nombre'   => 'Eren Admin',
            'correo'   => 'admin@utvt.edu.mx',
            'password' => Hash::make('admin12345'), // Encriptación Bcrypt
            'ID_rol'   => 3,
            'activo'   => 1
        ]);

        // 2. INSTRUCTOR (Rol 2)
        Usuario::create([
            'nombre'   => 'Juan Instructor',
            'correo'   => 'instructor@utvt.edu.mx',
            'password' => Hash::make('instru12345'),
            'ID_rol'   => 2,
            'activo'   => 1
        ]);

        // 3. ESTUDIANTE (Rol 1)
        Usuario::create([
            'nombre'   => 'Bianca Estudiante',
            'correo'   => 'bianca@utvt.edu.mx',
            'password' => Hash::make('estud12345'),
            'ID_rol'   => 1,
            'activo'   => 1
        ]);
    }
}