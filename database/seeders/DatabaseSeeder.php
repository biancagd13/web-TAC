<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Centraliza la ejecución de todos los seeders del SISTEMA TAC.
     */
    public function run(): void
    {
        // LLAMADA A LOS SEEDERS EN ORDEN JERÁRQUICO
        $this->call([
            // 1. Primero los roles, porque los usuarios dependen de que el ID_rol exista
            RolTableSeeder::class,
            
            // 2. Después los usuarios (Admin, Instructor, Estudiante)
            UsuariosTableSeeder::class,
            
            // Aquí puedes agregar más seeders en el futuro, por ejemplo:
            // CarreraTableSeeder::class,
            // TallerTableSeeder::class,
        ]);
    }
}