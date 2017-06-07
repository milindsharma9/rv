<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStoreDetailsAddStoreStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_details', function (Blueprint $table) {
            $table->tinyInteger('store_status')->default(1)->comment('1=> Active, 0=> Inactive');
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
            $table->dropColumn('store_status');
        });
    }
}
