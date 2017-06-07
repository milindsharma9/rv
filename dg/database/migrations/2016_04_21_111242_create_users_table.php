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
            $table->increments('id');
            $table->string('first_name', 64);
            $table->string('last_name', 64);
            $table->string('email', 64)->unique();
            $table->string('password', 64);
            $table->string('phone');
            $table->integer('fk_users_role', false)->unsigned();
            $table->rememberToken();
            $table->timestamps();
            $table->foreign('fk_users_role')->references('id_users_role')->on('users_role');
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