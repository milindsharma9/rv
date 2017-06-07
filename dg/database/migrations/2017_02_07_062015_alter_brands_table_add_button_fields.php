<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterBrandsTableAddButtonFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brand', function (Blueprint $table) {
            $table->string('button_text', 128)->after('url_path');
            $table->string('button_url', 255)->after('button_text');
            $table->enum('is_external', [0, 1])->default(0)->comment('0 => Internal Link, 1=> External Link')->after('button_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brand', function (Blueprint $table) {
            $table->dropColumn('button_text');
            $table->dropColumn('button_url');
            $table->dropColumn('is_external');
        });
    }
}
