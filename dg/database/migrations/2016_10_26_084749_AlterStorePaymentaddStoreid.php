<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStorePaymentaddStoreid extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('store_payment', function (Blueprint $table) {
            $table->integer('parent_store_id', false)->unsigned()->after('fk_store_id');
            $table->integer('fk_order_id', false)->unsigned()->after('parent_store_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('store_payment', function ($table) {
            $table->dropColumn('parent_store_id');
            $table->dropColumn('fk_order_id');
        });
    }

}
