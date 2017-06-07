<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterproductImagesAddThumb extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('product_images', function (Blueprint $table) {
            $table->tinyInteger('is_thumb')->default('0')->after('primary')->comment('0=> regular image, 1=> Thumb');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropColumn('is_thumb');
        });
    }

}
