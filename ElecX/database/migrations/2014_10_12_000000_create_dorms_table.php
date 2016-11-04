<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dorms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('area');
            $table->string('building');
            $table->string('dorm');
            $table->double('balance')->default('0');
            $table->double('remain_elec')->default('0');
            $table->boolean('notificationSwitch')->default(false);
            $table->boolean('notificationSent')->default(false);
            $table->smallInteger('notificationCount')->default('0');
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
        Schema::drop('dorms');
    }
}
