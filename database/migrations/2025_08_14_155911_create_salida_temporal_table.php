<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PARA CUANDO SE GENERA UN PDF, ANTES DE HACER UN GUARDADO
     */
    public function up(): void
    {
        Schema::create('salida_temporal', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->bigInteger('id_empleado')->unsigned();
            $table->string('descripcion', 800)->nullable();

            $table->foreign('id_empleado')->references('id')->on('empleado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salida_temporal');
    }
};
