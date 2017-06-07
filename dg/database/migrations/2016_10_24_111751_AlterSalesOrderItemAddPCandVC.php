<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSalesOrderItemAddPCandVC extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('sales_order_item', function (Blueprint $table) {
            $table->decimal('product_commission', 5, 2)->after('store_price');
            $table->decimal('vendor_commission', 5, 2)->after('product_commission');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('sales_order_item', function ($table) {
            $table->dropColumn('product_commission');
            $table->dropColumn('vendor_commission');
        });
    }

}
