<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->integer('activation');
            $table->integer('expiry');
            $table->string('description');
            $table->string('location');
            $table->string('type');
            $table->string('faction');
            $table->string('level');
            // Rewards
            $table->integer('credits')->unsigned()->nullable();
            $table->string('items')->nullable();
            $table->integer('stored_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('alerts');
    }
}
