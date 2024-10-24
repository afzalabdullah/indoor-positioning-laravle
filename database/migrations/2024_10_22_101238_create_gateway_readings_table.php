<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGatewayReadingsTable extends Migration
{
    public function up()
    {
        Schema::create('gateway_readings', function (Blueprint $table) {
            $table->id();
            $table->string('transmitter_serial_number');
            $table->string('node_type');
            $table->string('device_uid');
            $table->string('manufacturer_name');
            $table->integer('distance');
            $table->timestamp('time_stamp_utc');
            $table->integer('count');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gateway_readings');
    }
}
