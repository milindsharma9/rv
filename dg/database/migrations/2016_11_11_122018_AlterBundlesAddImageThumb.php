<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterBundlesAddImageThumb extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('bundles', function (Blueprint $table) {
            $table->string('image_thumb')->after('image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('bundles', function ($table) {
            $table->dropColumn('image_thumb');
        });
    }

}
