<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AltermangopayCardDetailsaddisDefault extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('mangopay_card_details', function (Blueprint $table) {
            $table->tinyInteger('is_default')->after('mango_users_card_id')->default('0')->comment('0=> Another Card, 1=> Default Card');
        });
        DB::unprepared("UPDATE `mangopay_card_details` SET is_default = 1");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('mangopay_card_details', function ($table) {
            $table->dropColumn('is_default');
        });
    }

}
