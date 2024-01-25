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
        Schema::create('sw_planets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->unsignedSmallInteger('rotation_period')->nullable();
            $table->unsignedSmallInteger('orbital_period')->nullable();
            $table->unsignedMediumInteger('diameter')->nullable();
            $table->string('climate')->nullable();
            $table->string('gravity')->nullable();
            $table->string('terrain')->nullable();
            $table->unsignedTinyInteger('surface_water')->nullable();
            $table->unsignedBigInteger('population')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sw_planets');
    }
};
