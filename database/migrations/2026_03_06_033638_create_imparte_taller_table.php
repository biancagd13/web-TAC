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
        Schema::create('imparte_taller', function (Blueprint $table) {
            $table->integer('ID_imparte')->autoIncrement();
            $table->string('periodo', 50);
            $table->date('fecha');
            $table->tinyInteger('activo');
            $table->integer('ID_usuario');
            $table->integer('ID_taller');
            $table->primary('ID_imparte');
            
            $table->foreign('ID_usuario')->references('ID_usuario')->on('usuarios');
            $table->foreign('ID_taller')->references('ID_taller')->on('talleres');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imparte_taller');
    }
};
