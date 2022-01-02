<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeparturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departures', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('departure_code')->unique()->comment('Mã chuyến');
            $table->bigInteger('user_id');
            $table->bigInteger('car_id');
            $table->integer('price')->default(0);
            $table->string('go_location_city')->comment('Địa điểm đi thành phố');
            $table->string('go_location_district')->comment('Địa điểm đi huyện');
            $table->string('go_location_wards')->comment('Địa điểm đi xã');
            $table->string('come_location_city')->comment('Địa điểm đến thành phố');
            $table->string('come_location_district')->comment('Địa điểm đến huyện');
            $table->string('come_location_wards')->comment('Địa điểm đến xã');
            $table->integer('seats_departures')->default(0)->comment('số ghế');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->timestamps();
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departures');
    }
}
