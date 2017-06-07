<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductsMetaAddPopularGifts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('products_meta', function (Blueprint $table) {
            $table->tinyInteger('is_popular')->default('0')->comment('0=> Not Popular, 1=> Popular');
            $table->tinyInteger('is_gifts')->default('0')->comment('0=> Not a gift, 1=> Gift.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_meta', function (Blueprint $table) {
           $table->dropColumn('is_popular');
           $table->dropColumn('is_gifts');
        });
    }
}
