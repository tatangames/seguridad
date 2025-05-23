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
        Schema::create('entradas_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_entradas')->unsigned();
            $table->bigInteger('id_material')->unsigned();
            $table->integer('cantidad');

            // SE IRA SUMANDO LA CANTIDAD ENTREGADA
            $table->integer('cantidad_entregada');

            $table->foreign('id_entradas')->references('id')->on('entradas');
            $table->foreign('id_material')->references('id')->on('materiales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entradas_detalle');
    }
};
