<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStoreDetailsAddLegalDetails extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('store_details', function (Blueprint $table) {
            $table->string('legal_fname', 255)->after('cname');
            $table->string('legal_lname', 255)->after('legal_fname');
            $table->date('legal_dob')->after('legal_lname'); 
            $table->string('nationality', 32)->after('legal_dob');
            $table->string('country_residence', 32)->after('nationality');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('store_details', function (Blueprint $table) {
            $table->dropColumn('legal_fname');
            $table->dropColumn('legal_lname');
            $table->dropColumn('legal_dob');
            $table->dropColumn('nationality');
            $table->dropColumn('country_residence');
        });
    }

}
