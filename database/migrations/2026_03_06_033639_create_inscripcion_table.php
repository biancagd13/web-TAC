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
        Schema::create('inscripcion', function (Blueprint $table) {
            $table->integer('ID_inscripción')->autoIncrement();
            $table->string('periodo', 50);
            $table->date('fecha');
            $table->integer('ID_carrera');
            $table->integer('ID_usuario');
            $table->integer('ID_taller');
            $table->primary('ID_inscripción');

            $table->foreign('ID_carrera')->references('ID_carrera')->on('carreras');
            $table->foreign('ID_usuario')->references('ID_usuario')->on('usuarios');
            $table->foreign('ID_taller')->references('ID_taller')->on('talleres');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripcion');
    }
};
