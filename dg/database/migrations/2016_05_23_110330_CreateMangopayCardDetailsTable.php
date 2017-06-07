<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMangopayCardDetailsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('mangopay_card_details', function (Blueprint $table) {
            $table->increments('id_mangopay_card_details');
            $table->integer('fk_users_id', false)->unsigned();
//            $table->integer('fk_user_mangopay_id', false)->unsigned();
            $table->integer('fk_card_addres_id', false)->unsigned();
            $table->integer('mango_users_card_id', false)->unsigned();
            $table->timestamps();
            $table->foreign('fk_users_id')->references('id')
                    ->on('users')->onDelete('cascade')->onUpdate('cascade');
//            $table->foreign('fk_user_mangopay_id')->references('id_user_mangopay')
//                    ->on('user_mangopay')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('fk_card_addres_id')->references('id_card_addres')
                    ->on('card_address')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('mangopay_card_details');
    }

}
