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
        Schema::create('carreras', function (Blueprint $table) {
            $table->integer('ID_carrera')->autoIncrement();
            $table->string('nombre', 100);
            $table->string('clave', 20);
            $table->text('detalle')->nullable();
            $table->tinyInteger('activo'); 
            $table->primary('ID_carrera');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carreras');
    }
};
