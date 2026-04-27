<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detalle_asistencias', function (Blueprint $table) {
            $table->integer('ID_detalle_asistencia')->autoIncrement();
            $table->date('fecha')->nullable();
            $table->boolean('entro')->default(0);
            $table->integer('ID_asistencia');
            $table->integer('ID_usuario');

            $table->primary('ID_detalle_asistencia');
            $table->index('fecha', 'idx_fecha_asistencia');
            $table->foreign('ID_asistencia')->references('ID_asistencia')->on('asistencias');
            $table->foreign('ID_usuario')->references('ID_usuario')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_asistencias');
    }
};
