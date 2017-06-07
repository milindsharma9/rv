<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendorLegalDetails extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('vendor_legal_address', function (Blueprint $table) {
            $table->increments('id_vendor_legal_address');
            $table->integer('fk_users_id', false)->unsigned();
            $table->string('address_line_1', 255);
             $table->string('address_line_2', 255);
            $table->string('city')->comment('Town');
            $table->string('region')->comment('Region');
            $table->string('pin')->comment('Post Code');
            $table->string('country');
            $table->string('nationality');
            $table->string('country_residence');
            $table->timestamp('updated_at');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('vendor_legal_address');
    }

}
