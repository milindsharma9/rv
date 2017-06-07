<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterValidPoatcodeAddLatLng extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('valid_postcodes', function (Blueprint $table) {
            $table->float('lat', 10, 6)->after('postcode');
            $table->float('lng', 10, 6)->after('lat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('valid_postcodes', function ($table) {
            $table->dropColumn('lat');
            $table->dropColumn('lng');
        });
    }
}
