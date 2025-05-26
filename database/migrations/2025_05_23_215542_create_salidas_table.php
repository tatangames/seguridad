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
        Schema::create('salidas', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha');
            $table->bigInteger('id_encargado')->unsigned();
            $table->bigInteger('id_distrito')->unsigned();
            $table->string('descripcion', 800)->nullable();

            $table->foreign('id_encargado')->references('id')->on('encargado');
            $table->foreign('id_distrito')->references('id')->on('distrito');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salidas');
    }
};
