<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('token_device')->nullable();
            $table->bigInteger('invoice_id')->nullable();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->string('status')->nullable()->comment('0 là chưa xem 1 là đã xem');
            $table->integer('is_send')->default(0)->comment('0 là gửi đi 1 là nhận');
            $table->bigInteger('role_id')->nullable();
            $table->string('avatar_notification')->nullable();
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
        Schema::dropIfExists('notifications');
    }
}
