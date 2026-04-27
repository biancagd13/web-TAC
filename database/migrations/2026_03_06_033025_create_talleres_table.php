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
        Schema::create('talleres', function (Blueprint $table) {
            $table->integer('ID_taller')->autoIncrement();
            $table->string('nombre', 100);
            $table->text('detalle')->nullable();
            
            // --- SOLO ESTOS 3 CAMPOS NUEVOS ---
            $table->integer('cupo')->default(20);          // Límite de alumnos
            $table->string('horario', 100)->nullable();    // Ej: "Lun y Mie 14:00 - 16:00"
            $table->string('periodo', 50)->nullable();    // Ej: "Enero-Abril 2026"
            
            $table->tinyInteger('activo')->default(1);
            $table->primary('ID_taller');
            $table->timestamps(); // Recomendado para control interno
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talleres');
    }
};