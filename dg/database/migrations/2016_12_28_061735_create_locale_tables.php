<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocaleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locale', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->string('sub_title', 1024);
            $table->text('description');
            $table->string('image', 255);
            $table->string('url_path', 255);
            $table->string('meta_title', 255);
            $table->text('meta_keywords', 255);
            $table->text('meta_description', 255);
            $table->enum('active', [0, 1])->default(0)->comment('0 => Inactive, 1=> Active');
            $table->timestamp('updated_at');
            $table->timestamp('created_at');
        });
        
        Schema::create('xref_locale_keyword', function (Blueprint $table) {
            $table->integer('fk_keyword_id', false)->unsigned();
            $table->integer('fk_locale_id', false)->unsigned();
            $table->primary(['fk_keyword_id', 'fk_locale_id']);
            
        });
        
        Schema::create('xref_locale_product', function (Blueprint $table) {
            $table->integer('fk_product_id', false)->unsigned();
            $table->integer('fk_locale_id', false)->unsigned();
            $table->primary(['fk_product_id', 'fk_locale_id']);
            
        });

        Schema::create('xref_locale_bundle', function (Blueprint $table) {
            $table->integer('fk_bundle_id', false)->unsigned();
            $table->integer('fk_locale_id', false)->unsigned();
            $table->tinyInteger('is_bundle');
            $table->primary(['fk_bundle_id', 'fk_locale_id']);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('locale');
        Schema::drop('xref_locale_keyword');
        Schema::drop('xref_locale_product');
        Schema::drop('xref_locale_bundle');
    }
}
