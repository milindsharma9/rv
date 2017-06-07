<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderQuestionResponseTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('driver_question_response', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fk_users_id', false)->unsigned();
            $table->integer('fk_question_id', false)->unsigned();
            $table->string('response', 20);
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
        Schema::drop('driver_question_response');
    }

}
