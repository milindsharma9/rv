<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterbrandTableAddImages extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('brand', function (Blueprint $table) {
            $table->string('image2', 255)->after('image');
            $table->string('image3', 255)->after('image2');
            $table->string('image4', 255)->after('image3');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('brand', function (Blueprint $table) {
            $table->dropColumn('image2');
            $table->dropColumn('image3');
            $table->dropColumn('image4');
        });
    }

}
