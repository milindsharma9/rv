<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRegisteredCardStatusTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('user_card_status', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('fk_users_id', false)->unsigned();
            $table->integer('cardId');
            $table->integer('cardAddressId');
            $table->string('status', 64)->comment('CREATED, ERROR, VALIDATED, REFUSED');
            $table->foreign('fk_users_id')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
            $table->timestamp('updated_at');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('user_card_status');
    }

}
