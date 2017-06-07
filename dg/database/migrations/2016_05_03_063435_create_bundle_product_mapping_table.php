<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBundleProductMappingTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('xref_bundle_products', function (Blueprint $table) {
            $table->integer('fk_bundle_id', false)->unsigned();
            $table->integer('fk_product_id', false)->unsigned();
            $table->integer('product_quantity', false)->unsigned();
            $table->primary(['fk_product_id', 'fk_bundle_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('xref_bundle_products');
    }

}
