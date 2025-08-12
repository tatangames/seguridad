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

            // ESTO BAJARA CUANDO SE DESCARTE
            $table->integer('cantidad');
            // HISTORIAL DE QUE ENTRO LA PRIMERA VEZ
            $table->integer('cantidad_inicial');


            // 4 DECIMALES PARA PRECIO UNITARIO
            $table->decimal('precio', 10,4);

            // SE IRA SUMANDO LA CANTIDAD ENTREGADA / RESTANDO CUANDO ENTRE DE NUEVO
            // QUEDARA UN REGISTRO POR CADA ITEM RECIBIDO
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
