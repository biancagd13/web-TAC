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
        Schema::create('constancias', function (Blueprint $table) {
            $table->integer('ID_constancia')->autoIncrement();
            $table->date('fecha_emision')->nullable();
            $table->integer('ID_usuario');
            $table->integer('ID_imparte');

            $table->primary('ID_constancia');
            $table->foreign('ID_usuario')->references('ID_usuario')->on('usuarios');
            $table->foreign('ID_imparte')->references('ID_imparte')->on('imparte_taller');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('constancias');
    }
};
