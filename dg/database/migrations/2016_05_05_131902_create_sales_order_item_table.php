<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesOrderItemTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sales_order_item', function (Blueprint $table) {
            $table->increments('id_sales_order_item');
            $table->integer('fk_sales_order_id', false)->unsigned();
            $table->integer('fk_product_id', false)->unsigned();
            $table->integer('fk_driver_id', false)->unsigned();
            $table->integer('fk_order_status_id', false)->unsigned();
            $table->integer('fk_store_id', false)->unsigned();
            $table->decimal('price', 5, 2);
            $table->timestamps();
            $table->foreign('fk_sales_order_id')->references('id_sales_order')
                    ->on('sales_order')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('fk_product_id')->references('id')
                    ->on('products')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('fk_driver_id')->references('id')
                    ->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('fk_order_status_id')->references('id_order_status')
                    ->on('order_status')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('fk_store_id')->references('id')
                    ->on('users')->onDelete('cascade')->onUpdate('cascade');
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
