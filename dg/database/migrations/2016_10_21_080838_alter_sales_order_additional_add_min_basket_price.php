<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSalesOrderAdditionalAddMinBasketPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_order_additional_info', function (Blueprint $table) {
            $table->decimal('min_basket_charge', 5, 2)->after('special_category_charge')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_order_additional_info', function ($table) {
            $table->dropColumn('min_basket_charge');
        });
    }
}
