<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_address', function (Blueprint $table) {
            $table->increments('id_store_address');
            $table->integer('fk_users_id', false)->unsigned();
            $table->string('address', 255);
            $table->string('city')->comment('Town');
            $table->string('state')->comment('Country');
            $table->string('pin')->comment('Post Code');
            $table->float('lat', 10, 6);
            $table->float('lng', 10, 6);
            $table->timestamp('updated_at');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('store_address');
    }
}
