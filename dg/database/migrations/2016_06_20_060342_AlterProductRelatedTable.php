<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductRelatedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_meta', function (Blueprint $table) {
            $table->text('safety_warnings')->after('allergy_advice');
            $table->text('allergy_other_text')->after('safety_warnings');
            $table->string('weight', 16)->change();
        });
        
        Schema::table('products', function (Blueprint $table) {
            $table->unique('barcode');
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
            $table->dropColumn('safety_warnings');
            $table->dropColumn('allergy_other_text');
        });
    }
}
