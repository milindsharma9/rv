<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayoutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payout', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fk_user_id', false)->unsigned();
            $table->integer('wallet_id')->comment('userWalletId');
            $table->integer('bank_account')->comment('userBankAccId');
            $table->decimal('amount', 10, 2)->comment('AmountTransferred');
            $table->integer('transaction_id')->comment('MangoTransactionId');
            $table->string('status')->comment('Payment Status');
            $table->text('rawData');
            $table->timestamps();
            $table->foreign('fk_user_id')->references('id')
                    ->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::drop('payout');
    }
}
