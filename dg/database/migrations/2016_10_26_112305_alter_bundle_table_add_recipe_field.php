<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterBundleTableAddRecipeField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('bundles', function (Blueprint $table) {
            $table->boolean('is_recipe')->default(0)->after('image');
            $table->boolean('is_popular')->default(0)->after('is_recipe');
            $table->boolean('is_gift')->default(0)->after('is_popular');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bundles', function ($table) {
            $table->dropColumn('is_recipe');
            $table->dropColumn('is_popular');
            $table->dropColumn('is_gift');
        });
    }
}
