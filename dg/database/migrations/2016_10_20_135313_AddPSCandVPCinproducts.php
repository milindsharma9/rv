<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPSCandVPCinproducts extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('products_meta', function (Blueprint $table) {
            $table->decimal('psc', 5, 2)->default('0.00')->comment('Product Site Commission');
            $table->decimal('vpc', 5, 2)->default('0.00')->comment('Vendor Product Commission');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('products_meta', function (Blueprint $table) {
            $table->dropColumn('psc');
            $table->dropColumn('vpc');
        });
    }

}
