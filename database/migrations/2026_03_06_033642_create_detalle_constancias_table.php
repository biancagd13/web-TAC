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
        Schema::create('detalle_constancias', function (Blueprint $table) {
            $table->integer('ID_detalle_constancia')->autoIncrement();
            $table->string('codigo_validacion', 100)->nullable();
            $table->text('firma_digital')->nullable();
            $table->dateTime('fecha_envio_email')->nullable();
            $table->integer('ID_constancia');

            $table->primary('ID_detalle_constancia');
            $table->foreign('ID_constancia')->references('ID_constancia')->on('constancias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_constancias');
    }
};
