<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreDetailsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('store_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('store_name', 64);
            $table->string('store_banner_image', 255);
            $table->integer('fk_users_id', false)->unsigned();
            $table->timestamps();
            $table->foreign('fk_users_id')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('store_details');
    }

}
