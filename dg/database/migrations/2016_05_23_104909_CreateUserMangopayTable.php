<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMangopayTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('user_mangopay', function (Blueprint $table) {
            $table->increments('id_user_mangopay');
            $table->integer('fk_users_id', false)->unsigned();
            $table->integer('mango_users_id', false)->unsigned();
            $table->integer('mango_users_wallet_id', false)->unsigned();
            $table->timestamps();
            $table->foreign('fk_users_id')->references('id')
                    ->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('user_mangopay');
    }

}
