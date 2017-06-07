<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesOrderMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_order_additional_info', function (Blueprint $table) {
            $table->increments('id_sales_order_additional_info');
            $table->integer('fk_sales_order_id', false)->unsigned();
            $table->decimal('after_midnight_charge', 5, 2)->default(0.00);
            $table->decimal('special_category_charge', 5, 2)->default(0.00);
            $table->enum('device_type', ['ios', 'android', 'web'])->default('web');
            $table->string('device_id')->nullable()->default(NULL);
            $table->timestamp('updated_at');
            $table->timestamp('created_at');
            $table->foreign('fk_sales_order_id')->references('id_sales_order')
                    ->on('sales_order')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sales_order_additional_info');
    }
}
