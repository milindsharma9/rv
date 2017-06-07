<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_order_tracking', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_id', 255);
            $table->string('job_id', 255);
            $table->tinyInteger('tookan_status_id')->default(6);
            $table->text('raw_data', 255);
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
        Schema::drop('sales_order_tracking');
    }
}
