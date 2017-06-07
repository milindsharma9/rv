<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewEnumIncouponTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared("ALTER TABLE coupon MODIFY COLUMN discount_type ENUM('P', 'F', 'D') COMMENT 'P => percentage, F => Flat, D => Delivery'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared("ALTER TABLE coupon MODIFY COLUMN discount_type ENUM('P', 'F') COMMENT 'P => percentage, F => Flat");
    }

}
