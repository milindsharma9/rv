<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOccasionEventAddSortOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->tinyInteger('sort_order')->default(100)->after('parent_id');
        });
        
        Schema::table('occasions', function (Blueprint $table) {
            $table->tinyInteger('sort_order')->default(100)->after('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function ($table) {
            $table->dropColumn('sort_order');
        });
        
        Schema::table('occasions', function ($table) {
            $table->dropColumn('sort_order');
        });
    }
}
