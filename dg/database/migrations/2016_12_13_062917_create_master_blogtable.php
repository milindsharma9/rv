<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterBlogtable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_blog', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', [0, 1, 2])->default(0);
            $table->string('title', 255);
            $table->string('sub_title', 255);
            $table->text('description');
            $table->string('image', 255);
            $table->string('image_thumb', 255);
            $table->string('url_path', 255);
            $table->string('meta_title', 255);
            $table->text('meta_keywords', 255);
            $table->text('meta_description', 255);
            $table->timestamp('updated_at');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->enum('published', [0, 1])->default(0)->comment('0 => draft, 1=> Published');
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
        Schema::drop('master_blog');
    }
}
