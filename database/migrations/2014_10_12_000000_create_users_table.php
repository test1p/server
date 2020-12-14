<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('provider_name')->nullable();
            $table->string('provider_uid')->nullable();
            $table->tinyInteger('role')->default(100);
            $table->string('name')->nullable();
            $table->string('furigana')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('sex')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('payment_method_id')->nullable();
            $table->string('subscription_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            $table->unique(['provider_name', 'provider_uid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
