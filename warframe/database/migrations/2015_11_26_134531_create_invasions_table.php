<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvasionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invasions', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->string('location');
            $table->double('progress', 6, 3);
            $table->string('attacker');
            $table->string('attackerReward');
            $table->string('defender');
            $table->string('defenderReward');
            $table->integer('activation');
            $table->integer('usec');
            $table->boolean('completed')->nullable()->default(false);
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
        Schema::drop('invasions');
    }
}
