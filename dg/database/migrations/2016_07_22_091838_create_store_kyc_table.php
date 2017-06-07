<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreKycTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_kyc_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fk_users_id', false)->unsigned();
            $table->integer('document_id', false)->unsigned();
            $table->string('type', 128)->comment('IDENTITY_PROOF, REGISTRATION_PROOF, ARTICLES_OF_ASSOCIATION, SHAREHOLDER_DECLARATION');
            $table->string('image');
            $table->string('status', 64)->comment('CREATED, VALIDATION_ASKED, VALIDATED, REFUSED');
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
        Schema::drop('user_kyc_details');
    }
}
