<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('transfer_plan_name');
            $table->integer('price');
            $table->tinyInteger('add_ticket_num');
            $table->boolean('plan_disclosure')->default(true);
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
        Schema::dropIfExists('transfer_plans');
    }
}
