<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fk_order_id', false)->unsigned();
            $table->integer('userMWalletId')->comment('DebitedWalletId');
            $table->integer('adminMWalletId')->comment('CreditedWalletId');
            $table->integer('userMId')->comment('AuthorId');
            $table->integer('adminMUserId')->comment('CreditedUserId');
            $table->integer('transferDataId')->comment('transferDataId');
            $table->integer('payInDAtanId')->comment('payInDAtaId');
            $table->integer('cardId')->comment('CardId');
            $table->text('rawData')->comment('DebitedWalletId');
            $table->timestamps();
            $table->foreign('fk_order_id')->references('id_sales_order')
                    ->on('sales_order')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('transaction_details');
    }

}
