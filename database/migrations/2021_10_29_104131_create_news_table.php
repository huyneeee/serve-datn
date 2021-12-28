<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('new_cate');
            $table->string('image')->nullable();
            $table->tinyInteger('act')->nullable()->comment('Kích Hoạt');
            $table->tinyInteger('hot')->nullable()->default(0)->comment('Bài Viết Hot');
            $table->text('short_content')->nullable();
            $table->text('content')->nullable();
            $table->string('slug')->unique();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
    }
}
