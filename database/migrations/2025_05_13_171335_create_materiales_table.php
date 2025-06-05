<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * MATERIALES
     */
    public function up(): void
    {
        Schema::create('materiales', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_medida')->unsigned();
            $table->bigInteger('id_marca')->unsigned();
            $table->bigInteger('id_normativa')->unsigned();
            $table->bigInteger('id_color')->unsigned();
            $table->bigInteger('id_talla')->unsigned();

            $table->string('nombre', 300);
            $table->string('codigo', 100)->nullable();

            $table->string('otros', 500)->nullable();

            $table->foreign('id_medida')->references('id')->on('unidad_medida');
            $table->foreign('id_marca')->references('id')->on('marca');
            $table->foreign('id_normativa')->references('id')->on('normativa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materiales');
    }
};
