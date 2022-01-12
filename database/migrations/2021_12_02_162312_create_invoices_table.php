<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('departure_id');
            $table->bigInteger('customers_id');
            $table->string('invoice_code')->unique()->comment('Mã thanh toán');
            $table->string('phone');
            $table->text('note');
            $table->string('name');
            $table->string('email');
            $table->string('go_point');
            $table->string('come_point');
            $table->string('quantity');
            $table->string('total_price');
            $table->date('date');
            $table->integer('status')->default(0)->comment('Trạng thái');
            $table->string('form_payment')->comment('Hình thức thanh toán');
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
        Schema::dropIfExists('invoices');
    }
}
