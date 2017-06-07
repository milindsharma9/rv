<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductsStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_store', function (Blueprint $table) {
            $table->decimal('vendor_price', 5, 2);
        });
        DB::unprepared("UPDATE `products_store` SET `vendor_price` = (SELECT products.store_price FROM products where products.id = products_store.fk_product_id)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_store', function (Blueprint $table) {
           $table->dropColumn('vendor_price');
        });
    }
}
