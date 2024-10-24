<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('email'); // Store user's email
            $table->string('name')->unique(); // Unique site name
            $table->text('description')->nullable(); // Site description
            $table->integer('assets')->default(0); // Number of assets, default to 0
            $table->string('image_url')->nullable(); // URL for the site image
            $table->timestamps(); // Laravel timestamps for created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('sites'); // Drop the sites table if it exists
    }
};
