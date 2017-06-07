<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogEventPlacesMetaTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('events_places_meta', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fk_master_blog_id', false)->unsigned();
            $table->string('location', 255);
            $table->string('address', 255);
            $table->string('city')->comment('Town');
            $table->string('state')->comment('Country');
            $table->string('pin')->comment('Post Code');
            $table->string('event_ticket_text', 255);
            $table->string('event_ticket_url', 255);
            $table->string('places_drink_text', 255);
            $table->string('places_drink_url', 255);
            $table->string('places_food_text', 255);
            $table->string('places_food_url', 255);
            $table->timestamp('updated_at');
            $table->timestamp('created_at');
            $table->foreign('fk_master_blog_id')->references('id')->on('master_blog');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('events_places_meta');
    }

}
