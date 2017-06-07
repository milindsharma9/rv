<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesOrderAddressTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sales_order_address', function (Blueprint $table) {
            $table->increments('id_sales_order_address');
            $table->string('first_name', 64);
            $table->string('last_name', 64);
            $table->string('address', 255);
            $table->string('phone');
            $table->string('city');
            $table->string('state');
            $table->string('pin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('sales_order_address');
    }

}
