<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStorePaymentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('store_payment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fk_store_id', false)->unsigned();
            $table->integer('payeeWalletId')->comment('storeWalletId');
            $table->integer('payerMWalletId')->comment('adminWalletId');
            $table->decimal('amount', 10, 2)->comment('AmountTransferred');
            $table->string('status')->comment('Payment Status');
            $table->text('rawData');
            $table->timestamps();
            $table->foreign('fk_store_id')->references('id')
                    ->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('store_payment');
    }

}
