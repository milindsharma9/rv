<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLoacleProductXref extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('xref_locale_product', function (Blueprint $table) {
            $table->tinyInteger('sort_order')->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('xref_locale_product', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
}
