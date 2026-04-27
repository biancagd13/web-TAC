<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->integer('ID_asistencia')->autoIncrement();
            $table->integer('ID_imparte');
            
            // CRUCIAL: Timestamp para medir la ventana de 12 horas
            $table->timestamp('fecha_creacion')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->primary('ID_asistencia');
            $table->foreign('ID_imparte')->references('ID_imparte')->on('imparte_taller');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};