<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveandaggregatecolumnsStoredetailsandLegalAddress extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        /**
         * adding data in new store_details columns (nationality & country residence) from vendor_legal_address.
         */
        DB::unprepared("UPDATE `store_details` AS SD INNER JOIN vendor_legal_address AS VA ON SD.fk_users_id = VA.fk_users_id SET SD.nationality = VA.nationality,
SD.country_residence = VA.country_residence");

        /**
         * Removing vendor_legal_address migrated columns.
         */
        Schema::table('vendor_legal_address', function (Blueprint $table) {
            $table->dropColumn('nationality');
            $table->dropColumn('country_residence');
        });

        /**
         * adding data in new store_details columns from director field to legal_lname, legal_fname, legal_dob.
         */
        DB::unprepared("CREATE TEMPORARY TABLE IF NOT EXISTS store_director_details AS (SELECT id,IF( LOCATE( '|', `director` ) >0, SUBSTRING_INDEX(SUBSTRING( `director` , 1, LOCATE( '|', `director` ) -1 ), ',', 1) , `director` ) AS lastname, 
IF( LOCATE( '|', `director` ) >0, SUBSTRING_INDEX(SUBSTRING( `director` , 1, LOCATE( '|', `director` ) -1 ), ',', -1) , `director` ) AS firstname,
IF( LOCATE( '|', `director` ) >0, CONCAT_WS('-',SUBSTRING_INDEX(SUBSTRING( `director` , LOCATE( '|', `director` ) +1 ) , '|', 1),  IF (LENGTH(SUBSTRING_INDEX(SUBSTRING( `director` , LOCATE( '|', `director` ) +1 ) , '|', -1)) = 2,SUBSTRING_INDEX(SUBSTRING( `director` , LOCATE( '|', `director` ) +1 ) , '|', -1),CONCAT('0', SUBSTRING_INDEX(SUBSTRING( `director` , LOCATE( '|', `director` ) +1 ) , '|', -1))), '01'), NULL ) AS Year
FROM `store_details`)");
        DB::unprepared("UPDATE `store_details` AS SD INNER JOIN store_director_details AS SDD ON SD.id = SDD.id SET SD.legal_fname = SDD.firstname,
SD.legal_lname = SDD.lastname, 
SD.legal_dob=SDD.Year");
        DB::unprepared("DROP TEMPORARY TABLE IF EXISTS store_director_details;");

        /**
         * Removing store_details migrated columns.
         */
        Schema::table('store_details', function (Blueprint $table) {
            $table->dropColumn('director');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        /**
         * Rollback removed vendor_legal_address columns.
         */
        Schema::table('vendor_legal_address', function (Blueprint $table) {
            $table->string('country_residence');
            $table->string('nationality');
        });


        /**
         * Populated newly created columns with data. 
         */
        DB::unprepared("UPDATE `store_details` AS SD INNER JOIN vendor_legal_address AS VA ON SD.fk_users_id = VA.fk_users_id SET VA.nationality = SD.nationality,
VA.country_residence = SD.country_residence");

        /**
         * Rollback removed store_details columns.
         */
        Schema::table('store_details', function (Blueprint $table) {
            $table->string('director');
        });

        /**
         * Populated newly created columns with data. 
         */
        DB::unprepared("CREATE TEMPORARY TABLE IF NOT EXISTS store_director_details AS  (SELECT id,CONCAT_WS('|',CONCAT_WS(', ', legal_lname, legal_fname),SUBSTRING_INDEX(legal_dob, '-', 1),SUBSTRING( legal_dob, -5, LOCATE( '-', legal_dob ) -3 )) AS director FROM store_details)");
        DB::unprepared("UPDATE `store_details` AS SD INNER JOIN store_director_details AS SDD ON SD.id = SDD.id SET SD.director = SDD.director");
        DB::unprepared("DROP TEMPORARY TABLE IF EXISTS store_director_details");
    }

}
