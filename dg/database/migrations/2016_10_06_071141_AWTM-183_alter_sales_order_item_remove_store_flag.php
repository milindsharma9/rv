<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AWTM183AlterSalesOrderItemRemoveStoreFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_order_item', function ($table) {
            $table->dropColumn('store_flag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_order_item', function (Blueprint $table) {
            $table->tinyInteger('store_flag')->default(0);
        });
    }
}
