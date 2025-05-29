<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PERSONAS
     */
    public function up(): void
    {
        Schema::create('encargado', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);

            $table->string('telefono', 8)->nullable();
            $table->string('puesto', 200)->nullable();
            $table->string('dui',10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encargado');
    }
};
