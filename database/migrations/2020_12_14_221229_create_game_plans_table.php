<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('game_plan_name');
            $table->integer('unit_price');
            $table->tinyInteger('general_ticket_cost');
            $table->tinyInteger('student_ticket_cost');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_plans');
    }
}
