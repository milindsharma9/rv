<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Alterbundletablechangeservesdatatype extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bundles', function (Blueprint $table) {
            $table->string('serves')->change();
        });
        DB::unprepared("UPDATE `bundles` SET `serves` = IF(serves = 0 ,'',CONCAT('Serves: ',serves));");
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bundles', function (Blueprint $table) {
            $table->integer('serves')->change();
        });
    }
}
