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
        Schema::create('avisos', function (Blueprint $table) {
                $table->integer('ID_aviso')->autoIncrement();
                $table->string('titulo', 100)->nullable();
                $table->text('contenido')->nullable();
                $table->date('fecha_publicacion')->nullable();
                $table->integer('ID_usuario');

                $table->primary('ID_aviso');
                // Esta es la restricción que fallaba
                $table->foreign('ID_usuario')->references('ID_usuario')->on('usuarios');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avisos');
    }
};
