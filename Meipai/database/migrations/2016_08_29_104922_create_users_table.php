<?php

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
            $table->bigIncrements('id');
            $table->string('name', 64);
            $table->bigInteger('stats')->default(0);    // 发送美拍数量
            $table->bigInteger('following')->default(0);    // 关注数量
            $table->bigInteger('follower')->default(0); // 关注者数量
            $table->time('timeline_created')->default(0);   // feed 流
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
        Schema::drop('users');
    }
}
