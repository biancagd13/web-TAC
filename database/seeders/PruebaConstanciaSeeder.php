<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PruebaConstanciaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buscamos a Bianca
        $usuario = DB::table('usuarios')->where('nombre', 'like', '%Bianca%')->first();

        if (!$usuario) {
            $this->command->error('No se encontró a la usuaria Bianca en la tabla usuarios.');
            return;
        }

        // 2. Buscamos el primer taller disponible en imparte_taller
        $imparte = DB::table('imparte_taller')->first();

        if (!$imparte) {
            $this->command->error('No hay talleres en imparte_taller. Crea uno primero.');
            return;
        }

        $this->command->info('Generando 10 sesiones de asistencia para Bianca...');

        // 3. Crear 10 clases y registrar asistencia en cada una
        for ($i = 0; $i < 10; $i++) {
            // INSERTAR SOLO ID_imparte (evitamos created_at que no existe)
            $asistenciaId = DB::table('asistencias')->insertGetId([
                'ID_imparte' => $imparte->ID_imparte,
            ]);

            // 4. Registrar el detalle (aquí sí usamos la fecha para el registro del alumno)
            DB::table('detalle_asistencias')->insert([
                'fecha'         => Carbon::now()->subDays($i)->toDateString(),
                'entro'         => 1,
                'ID_asistencia' => $asistenciaId,
                'ID_usuario'    => $usuario->ID_usuario,
            ]);
        }

        // 5. Crear la Constancia oficial
        $constanciaId = DB::table('constancias')->insertGetId([
            'fecha_emision' => Carbon::now()->toDateString(),
            'ID_usuario'    => $usuario->ID_usuario,
            'ID_imparte'    => $imparte->ID_imparte,
        ]);

        // 6. Crear el Detalle de Validación (Alexa/Folio)
        DB::table('detalle_constancias')->updateOrInsert(
            ['ID_constancia' => $constanciaId],
            [
                'codigo_validacion' => 'TAC-CORRECTO-' . strtoupper(bin2hex(random_bytes(3))),
                'firma_digital'     => 'FIRMA_VALIDA_' . bin2hex(random_bytes(4)),
                'fecha_envio_email' => Carbon::now(),
            ]
        );

        $this->command->info('-----------------------------------------');
        $this->command->info('¡ÉXITO TOTAL!');
        $this->command->info('Bianca ahora tiene 10 clases registradas.');
        $this->command->info('La constancia ha sido generada correctamente.');
        $this->command->info('-----------------------------------------');
    }
}