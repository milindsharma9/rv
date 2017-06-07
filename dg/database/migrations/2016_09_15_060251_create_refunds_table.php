<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refund_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('refund_id')->comment('MangoPay Refund Id');
            $table->integer('intital_transaction_id')->comment('Refund Against Transaction');
            $table->string('refund_message')->comment('CreditedWalletId');
            $table->string('refund_status')->comment('MangoPay Status');
            $table->text('rawData')->comment('raw mangopay data');
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
        Schema::drop('refund_details');
    }
}
