<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTokenToUser extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('users', function ($table) {
            $table->string('token')->after('remember_token');
            $table->boolean('activated')->default(false)->after('token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('users', function ($table) {
            $table->dropColumn('token');
            $table->dropColumn('activated');
        });
    }

}
