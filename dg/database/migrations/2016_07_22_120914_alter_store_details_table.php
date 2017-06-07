<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStoreDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_details', function (Blueprint $table) {
            $table->enum('business_type', ['BUSINESS', 'ORGANIZATION', 'SOLETRADER'])->default('SOLETRADER')->after('store_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_details', function ($table) {
            $table->dropColumn('business_type');
        });
    }
}
