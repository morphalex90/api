<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableStar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('star', function (Blueprint $table) {
            $table->id();
            $table->integer('vote');
            $table->ipAddress('ip');
            $table->timestamp('created_at')->nullable();
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *m
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('star');
    }
}
