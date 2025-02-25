<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * REGISTRO DE COBROS DE CADA PERSONA
     */
    public function up(): void
    {
        Schema::create('nicho_cobros', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_nichomunicipal_detalle')->unsigned();
            $table->string('nombre', 100)->nullable();
            $table->string('dui', 15)->nullable();
            $table->string('telefono', 10)->nullable();
            $table->string('direccion', 300)->nullable();
            $table->integer('periodo'); // CUANTOS PERIODOS PAGA EL USUARIO

            // Tesoreria
            $table->string('recibo', 50)->nullable();
            $table->date('fecha_recibo')->nullable();

            $table->foreign('id_nichomunicipal_detalle')->references('id')->on('nicho_municipal_detalle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nicho_cobros');
    }
};
