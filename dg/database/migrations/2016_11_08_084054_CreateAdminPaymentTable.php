<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_payment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fk_order_id', false)->unsigned();
            $table->integer('payeeWalletId')->comment('adminWalletId');
            $table->integer('payerMWalletId')->comment('Customer Wallet Id');
            $table->decimal('amount', 10, 2)->comment('AmountTransferred');
            $table->string('status')->comment('Payment Status');
            $table->text('rawData');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::drop('admin_payment');
    }
}
