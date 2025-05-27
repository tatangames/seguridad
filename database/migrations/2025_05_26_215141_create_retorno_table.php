<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * RETORNOS
     */
    public function up(): void
    {
        Schema::create('retorno', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            // PERSONA QUE DEVOLVIO
            $table->bigInteger('id_encargado')->unsigned();
            $table->string('observacion', 800)->nullable();

            // CANTIDAD REINGRESO
            $table->integer('cantidad_reingreso');

            // TIPO DE RETORNO
            // 0: retorno
            // 1: descarte
            $table->boolean('tipo_retorno');


            $table->foreign('id_encargado')->references('id')->on('encargado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retorno');
    }
};
