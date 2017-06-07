<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserCoupon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('user_coupon', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('fk_users_id', false)->unsigned();
            $table->integer('fk_coupon_id', false)->unsigned();
            $table->foreign('fk_users_id')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
             $table->foreign('fk_coupon_id')
                    ->references('id')
                    ->on('coupon')->onDelete('cascade')
                    ->onUpdate('cascade');
            $table->timestamp('updated_at');
            $table->timestamp('created_at');
            $table->timestamp('date_used');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_coupon');
    }
}
