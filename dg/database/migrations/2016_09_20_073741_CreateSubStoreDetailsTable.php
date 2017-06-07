<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubStoreDetailsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sub_store_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('store_name', 64);
            $table->integer('fk_parent_id', false)->unsigned()->default(0)->comment('0=> No Parent, id represent parent, related to users:id');
            $table->integer('fk_users_id', false)->unsigned();
            $table->tinyInteger('store_status')->default(1)->comment('1=> Active, 0=> Inactive');
            $table->timestamp('updated_at');
            $table->timestamp('created_at');
            $table->foreign('fk_users_id')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
        });

        DB::unprepared("INSERT INTO `sub_store_details`( `store_name`,  `fk_users_id`) select store_details.`store_name`, store_details.fk_users_id from store_details where store_details.`store_name` != '';");

        Schema::table('store_details', function (Blueprint $table) {
            $table->dropColumn('store_banner_image');
            $table->dropColumn('store_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('store_details', function (Blueprint $table) {
            $table->string('store_name', 64);
            $table->string('store_banner_image', 255);
        });
        DB::unprepared("UPDATE `store_details` SET `store_name`= (SELECT sub_store_details.store_name from sub_store_details where sub_store_details.fk_parent_id = 0  AND store_details.fk_users_id = sub_store_details.fk_users_id)");
        Schema::drop('sub_store_details');
    }

}
