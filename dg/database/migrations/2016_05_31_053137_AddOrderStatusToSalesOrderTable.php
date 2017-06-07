<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderStatusToSalesOrderTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('sales_order', function (Blueprint $table) {
            $table->integer('fk_order_status_id', false)->default(4)->unsigned()->after('fk_sales_address_id');
            $table->foreign('fk_order_status_id')->references('id_order_status')
                    ->on('order_status')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('sales_order', function ($table) {
            $table->dropColumn('fk_order_status_id');
        });
    }

}
