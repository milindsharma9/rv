<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Coupon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon', function (Blueprint $table) {

            $table->increments('id');
            $table->string('coupon_code');
            $table->enum('discount_type', ['P', 'F'])->comment('P => percentage, F => Flat');
            $table->decimal('discount_amount', 5, 2);
            $table->integer('usage')->default(0)->comment('Number of times the code has been used');
            $table->timestamp('updated_at');
            $table->timestamp('created_at');
            $table->timestamp('date_expiry');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('coupon');
    }
}
