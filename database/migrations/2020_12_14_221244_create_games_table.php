<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('event_id')->index();
            $table->uuid('game_category_id')->index();
            $table->uuid('game_plan_id')->index();
            $table->uuid('timekeeping_card_id')->index()->nullable();
            $table->string('game_name')->nullable();
            $table->date('date');
            $table->string('venue');
            $table->boolean('team_game')->default(false);
            $table->dateTime('entry_started_at')->nullable();
            $table->dateTime('entry_ended_at')->nullable();
            $table->integer('capacity')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('event_id')->references('id')->on('events');
            $table->foreign('game_category_id')->references('id')->on('game_categories');
            $table->foreign('game_plan_id')->references('id')->on('game_plans');
            $table->foreign('timekeeping_card_id')->references('id')->on('timekeeping_cards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
}
