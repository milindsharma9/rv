<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOcccaionEventsTableAddBannerImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('occasions', function (Blueprint $table) {
            $table->string('image_banner')->after('image');
        });
        
        Schema::table('events', function (Blueprint $table) {
            $table->string('image_banner')->after('image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('occasions', function ($table) {
            $table->dropColumn('image_banner');
        });

        Schema::table('events', function ($table) {
            $table->dropColumn('image_banner');
        });
    }
}
