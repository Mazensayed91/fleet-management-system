<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cross_over_stations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('trip_id');
            $table->unsignedBigInteger('start_station_id');
            $table->unsignedBigInteger('end_station_id');
            $table->unsignedBigInteger('bus_id');

            $table->integer('station_order');
            $table->integer('available_seats');
            $table->timestamps();

            $table->index(['start_station_id', 'end_station_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cross_over_stations');
    }
};
