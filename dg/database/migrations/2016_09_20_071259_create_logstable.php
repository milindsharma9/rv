<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogstable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('al_event_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('users_id');
            $table->timestamp('created_at');
            $table->string('operation_type', 255);
            $table->string('ip_address', 255);
            $table->text('al_event');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('al_event_log');
    }
}
