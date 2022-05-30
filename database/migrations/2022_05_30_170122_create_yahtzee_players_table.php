<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYahtzeePlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yahtzee_players', function (Blueprint $table) {
            $table->id();
            $table->string('playername', 40);
            $table->unsignedInteger('points')->default(0);
            $table->string('status', 20);
            $table->unsignedBigInteger('session_id');
            $table->index('session_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yahtzee_players');
    }
}
