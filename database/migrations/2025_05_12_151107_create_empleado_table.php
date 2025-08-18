<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * EMPLEADOS
     */
    public function up(): void
    {
        Schema::create('empleado', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);

            $table->bigInteger('id_unidad_empleado')->unsigned();
            $table->bigInteger('id_cargo')->unsigned();

            $table->boolean('jefe');

            $table->string('dui', 50)->nullable();

            $table->foreign('id_unidad_empleado')->references('id')->on('unidad_empleado');
            $table->foreign('id_cargo')->references('id')->on('cargo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleado');
    }
};
