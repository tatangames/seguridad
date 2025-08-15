<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * * DETALLE DE SALIDA TEMPORAL PARA GENERAR UN PDF
     */
    public function up(): void
    {
        Schema::create('salida_detalle_temporal', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_salida')->unsigned();
            $table->bigInteger('id_entrada_detalle')->unsigned();

            $table->integer('cantidad_salida');

            $table->boolean('reemplazo');
            $table->boolean('recomendacion');

            $table->foreign('id_salida')->references('id')->on('salida_temporal');
            $table->foreign('id_entrada_detalle')->references('id')->on('entradas_detalle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salida_detalle_temporal');
    }
};
