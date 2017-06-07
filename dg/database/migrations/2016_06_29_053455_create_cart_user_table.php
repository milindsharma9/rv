<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_cart_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id', 128)->unique();
            $table->text('data')->comment('cart_data Collection Object');
            $table->integer('total_quantity')->comment('Cart items count');
            $table->timestamp('updated_at');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_cart_data');
    }
}
