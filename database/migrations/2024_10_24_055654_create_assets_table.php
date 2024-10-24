<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID for the assets
            $table->string('device_uid')->unique(); // Unique Device Name
            $table->string('device_icon'); // Device Icon URL
            $table->string('device_name');
            $table->foreignId('site_id')->constrained()->onDelete('cascade'); // Foreign key for sites table
            $table->timestamps(); // Laravel timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('assets'); // Drop the assets table if it exists
    }
}
