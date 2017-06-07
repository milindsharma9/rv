<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateuserSubscriptionTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('user_subscription', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fk_users_id', false)->unsigned();
            $table->tinyInteger('is_subscribed')->default(1)->comment('1=> subscribed, 0 => not subscribed');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('created_at');
            $table->foreign('fk_users_id')->references('id')
                    ->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
        DB::unprepared("INSERT INTO `user_subscription`(`fk_users_id`) SELECT users.id FROM users where users.fk_users_role = 3");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('user_subscription');
    }

}
