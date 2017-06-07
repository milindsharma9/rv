<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCategoryMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xref_product_categories', function (Blueprint $table) {
            $table->integer('fk_product_id', false)->unsigned();
            $table->integer('fk_category_id', false)->unsigned();
            $table->primary(['fk_product_id', 'fk_category_id']);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::drop('xref_product_categories');
    }
}
