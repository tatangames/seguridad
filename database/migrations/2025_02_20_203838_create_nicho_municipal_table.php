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
        Schema::create('nicho_municipal', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_libros')->unsigned();
            $table->integer('correlativo');

            $table->foreign('id_libros')->references('id')->on('libros');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nicho_municipal');
    }
};
