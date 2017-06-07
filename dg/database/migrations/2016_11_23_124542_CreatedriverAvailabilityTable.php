<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatedriverAvailabilityTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('driver_availability', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fk_users_id', false)->unsigned();
            $table->enum('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            $table->integer('fk_time_id', false)->unsigned();
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
        Schema::drop('driver_availability');
    }

}
