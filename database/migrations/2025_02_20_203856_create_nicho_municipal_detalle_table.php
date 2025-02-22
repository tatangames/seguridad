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
        Schema::create('nicho_municipal_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_nicho_municipal')->unsigned();
            $table->string('nombre', 100);
            $table->date('fecha_fallecimiento');
            $table->date('fecha_exhumacion')->nullable();

            // periodo mora es calculado
            // fecha vencimiento es calculado

            $table->foreign('id_nicho_municipal')->references('id')->on('nicho_municipal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nicho_municipal_detalle');
    }
};
