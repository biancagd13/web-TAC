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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->integer('ID_usuario')->autoIncrement();
            $table->string('nombre', 100);
            $table->string('correo', 100)->unique();
            $table->string('password', 255);
            $table->string('telefono', 20)->nullable(); // Nuevo: Para WhatsApp
            $table->longText('foto_perfil')->nullable(); // Nuevo: Para Base64
            $table->integer('ID_rol'); 
            $table->boolean('activo')->default(true); 
            
            $table->primary('ID_usuario');
            $table->foreign('ID_rol')->references('ID_rol')->on('rol');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};