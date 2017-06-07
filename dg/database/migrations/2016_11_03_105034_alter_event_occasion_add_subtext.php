<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEventOccasionAddSubtext extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('floating_text')->after('image_logo');
            $table->string('sub_text')->after('floating_text');
        });
        
        Schema::table('occasions', function (Blueprint $table) {
            $table->string('floating_text')->after('image_logo');
            $table->string('sub_text')->after('floating_text');
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
            $table->dropColumn('floating_text');
            $table->dropColumn('sub_text');
        });
        
        Schema::table('occasions', function ($table) {
            $table->dropColumn('floating_text');
            $table->dropColumn('sub_text');
        });
    }
}
