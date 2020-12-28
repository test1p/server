<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_classes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('class_name');
            $table->tinyInteger('min_age');
            $table->tinyInteger('max_age');
            $table->tinyInteger('min_member_num');
            $table->tinyInteger('max_member_num');
            $table->tinyInteger('difficulty');
            $table->tinyInteger('distance');
            $table->tinyInteger('women_only');
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
        Schema::dropIfExists('event_classes');
    }
}
