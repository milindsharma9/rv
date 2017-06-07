<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderDetailsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('driver_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fk_users_id', false)->unsigned();
            $table->enum('vehicle', ['bicycle', 'scooter'])->default('bicycle');
            $table->string('address_line_1', 255);
            $table->string('address_line_2', 255);
            $table->string('city')->comment('Town');
            $table->string('region')->comment('Region');
            $table->string('pin')->comment('Post Code');
            $table->string('country');
            $table->string('nationality', 32);
            $table->integer('fk_occupation_id', false)->unsigned();
            $table->boolean('is_right_to_work')->comment('1 => Right to work in uk, 0=> no right to work')->default('0');
            $table->boolean('east_london')->comment('1 => yes, 0=> no')->default('0');
            $table->boolean('central_london')->comment('1 => yes, 0=> no')->default('0');
            $table->boolean('south_london')->comment('1 => yes, 0=> no')->default('0');
            $table->boolean('west_london')->comment('1 => yes, 0=> no')->default('0');
            $table->timestamp('updated_at');
            $table->timestamp('created_at');
            $table->foreign('fk_users_id')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('driver_details');
    }

}
