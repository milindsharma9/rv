<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaqUserGroupMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faq_group_mapping', function (Blueprint $table) {
            $table->integer('fk_faq_id', false)->unsigned();
            $table->integer('fk_user_group_id', false)->unsigned();
            $table->primary(['fk_faq_id', 'fk_user_group_id']);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('faq_group_mapping');
    }
}
