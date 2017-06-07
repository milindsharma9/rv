<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesOrderTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sales_order', function (Blueprint $table) {
            $table->increments('id_sales_order');
            $table->string('order_id');
            $table->integer('fk_users_id', false)->unsigned();
            $table->integer('fk_sales_address_id', false)->unsigned();
            $table->decimal('total', 5, 2);
            $table->decimal('rider_charges', 5, 2);
            $table->decimal('estimated_delivery_time', 5, 2);
            $table->timestamps();
            $table->foreign('fk_users_id')->references('id')
                    ->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('fk_sales_address_id')
                    ->references('id_sales_order_address')
                    ->on('sales_order_address')->onDelete('cascade')
                    ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('sales_order');
    }

}
