<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVendorPriceToSalesOrderItemTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('sales_order_item', function (Blueprint $table) {
            $table->decimal('store_price', 5, 2)->after('price');
            $table->tinyInteger('store_flag')->after('store_price')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('sales_order_item', function ($table) {
            $table->dropColumn('store_price');
            $table->dropColumn('store_flag');
        });
    }

}
