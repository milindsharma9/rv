<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductsMetaAddDataFeed extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('products_meta', function (Blueprint $table) {
            $table->tinyInteger('in_data_feed')->default('1')->comment('0=> not included in feeds, 1=> included in feeds.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('products_meta', function (Blueprint $table) {
            $table->dropColumn('in_data_feed');
        });
    }

}
