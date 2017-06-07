<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBundleEventsMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xref_bundle_events', function (Blueprint $table) {
            $table->integer('fk_bundle_id', false)->unsigned();
            $table->integer('fk_event_id', false)->unsigned();
            $table->primary(['fk_bundle_id', 'fk_event_id']);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('xref_bundle_events');
    }
}
