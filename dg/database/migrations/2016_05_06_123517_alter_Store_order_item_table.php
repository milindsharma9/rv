<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStoreOrderItemTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('sales_order_item', function (Blueprint $table) {
            $table->integer('GTIN')->after('fk_product_id'); //the after method is optional.
            $table->integer('bundle_id')->after('GTIN');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('sales_order_item', function ($table) {
            $table->dropColumn('GTIN');
            $table->dropColumn('bundle_id');
        });
    }

}
