<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductsMeta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_meta', function ($table) {
            $table->string('brand_family');
            $table->string('product_group');
            $table->string('units_per_pack');
            $table->string('size');
            $table->string('abv');
            $table->boolean('lower_age_limit_new');
            $table->boolean('safety_warnings_new');
            $table->string('packaging');
            $table->string('features');
            $table->string('meta_keywords');
            $table->string('meta_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_meta', function ($table) {
            $table->dropColumn('brand_family');
            $table->dropColumn('product_group');
            $table->dropColumn('units_per_pack');
            $table->dropColumn('size');
            $table->dropColumn('abv');
            $table->dropColumn('lower_age_limit_new');
            $table->dropColumn('safety_warnings_new');
            $table->dropColumn('packaging');
            $table->dropColumn('meta_keywords');
            $table->dropColumn('meta_description');
            $table->dropColumn('features');
        });
    }
}
