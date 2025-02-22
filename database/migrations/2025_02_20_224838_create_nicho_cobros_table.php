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

            // LA PRIMERA VEZ QUE SE HACE UN REGISTRO, ESTO SERA LA FECHA DEL FALLECIDO
            // YA CUANDO SE REGISTRA UN COBRO SE TOMA LA FECHA DE RECIBO

            // NO BORRAR (fecha_recibo) PORQUE AL REGISTRAR NUEVO, PUEDE SER NULL
            $table->date('fecha_ciclo');

            $table->string('nombre', 100);
            $table->string('dui', 15)->nullable();
            $table->string('telefono', 10)->nullable();
            $table->string('direccion', 300)->nullable();

            $table->integer('periodo'); // CUANTOS PERIODOS PAGA EL USUARIO

            // Sera Costo de $20.00 por LEY
            $table->decimal('base_costo', 10,2);

            // Periodo de cobro segun LEY (7 Años)
            $table->integer('anios_cobro');

            // costo sin el 5% y con el Son Calculado
            // Sera Costo fiesta patronales (5%)
            $table->decimal('base_comision', 10,2);

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
