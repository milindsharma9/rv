<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_store', function (Blueprint $table) {
            $table->integer('fk_product_id', false)->unsigned();
            $table->integer('fk_user_id', false)->unsigned();
            $table->primary(['fk_product_id', 'fk_user_id']);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('products_store');
    }
}
