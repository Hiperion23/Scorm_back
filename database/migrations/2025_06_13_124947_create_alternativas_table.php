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
        Schema::create('alternativas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotspot_id');
            $table->string('opcion');
            $table->integer('ancho');
            $table->integer('alto');
            $table->integer('eje_x');
            $table->integer('eje_y');
            $table->integer('radio');
            $table->string('img_rpta')->nullable();

            $table->foreign('hotspot_id')->references('id')->on('hotspots')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alternativas');
    }
};
