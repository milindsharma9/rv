<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->string('sub_title', 1024);
            $table->string('image', 255);
            $table->string('image_background', 255);
            $table->string('url_path', 255);
            $table->string('meta_title', 255);
            $table->text('meta_keywords', 255);
            $table->text('meta_description', 255);
            $table->enum('active', [0, 1])->default(0)->comment('0 => Inactive, 1=> Active');
            $table->timestamp('updated_at');
            $table->timestamp('created_at');
        });
        
        Schema::create('xref_brand_product', function (Blueprint $table) {
            $table->integer('fk_product_id', false)->unsigned();
            $table->integer('fk_brand_id', false)->unsigned();
            $table->tinyInteger('sort_order');
            $table->primary(['fk_product_id', 'fk_brand_id']);
            
        });

        Schema::create('xref_brand_bundle', function (Blueprint $table) {
            $table->integer('fk_bundle_id', false)->unsigned();
            $table->integer('fk_brand_id', false)->unsigned();
            $table->tinyInteger('is_bundle');
            $table->primary(['fk_bundle_id', 'fk_brand_id', 'is_bundle']);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('brand');
        Schema::drop('xref_brand_product');
        Schema::drop('xref_brand_bundle');
    }
}
