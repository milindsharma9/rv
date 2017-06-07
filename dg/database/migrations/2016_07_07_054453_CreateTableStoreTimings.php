<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableStoreTimings extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('store_timings', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            $table->integer('fk_user_id', false)->unsigned();
            $table->tinyInteger('is_closed')->default(1)->comment('1=> Closed, 0=> Open');
            $table->tinyInteger('is_24hrs')->default(0)->comment('1=> Open 24*7, 0=> Open @ particular time');
            $table->time('open_time');
            $table->time('close_time');
            $table->timestamp('updated_at');
            $table->foreign('fk_user_id')->references('id')
                    ->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('store_timings');
    }

}
