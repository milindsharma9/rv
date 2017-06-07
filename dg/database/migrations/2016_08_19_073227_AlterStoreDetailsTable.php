<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStoreDetailsAddCompanyDataTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('store_details', function (Blueprint $table) {
            $table->string('cname', 255)->after('store_name')->nullable();
            $table->string('pln', 255)->after('cname')->nullable()->comment('Premise Licence Number');
            $table->string('dps', 255)->after('pln')->nullable()->comment('Designated Premise Supervisor');
            $table->string('licence_number', 255)->nullable()->after('dps')->comment('Personal Licence Number');
            $table->string('director', 255)->nullable()->after('licence_number');
            $table->string('company_number', 255)->nullable()->after('director')->comment('Company Id retrieved by comapny house');
            $table->string('company_type', 255)->nullable()->after('company_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('store_details', function ($table) {
            $table->dropColumn('cname');
            $table->dropColumn('pln');
            $table->dropColumn('dps');
            $table->dropColumn('licence_number');
            $table->dropColumn('director');
            $table->dropColumn('company_number');
            $table->dropColumn('company_type');
        });
    }

}
