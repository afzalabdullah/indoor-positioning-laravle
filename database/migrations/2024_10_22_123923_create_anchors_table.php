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
        Schema::create('anchors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade'); // Reference to the site
            $table->string('uid')->unique(); // Unique identifier for the anchor
            $table->decimal('x', 10, 4); // X coordinate
            $table->decimal('y', 10, 4); // Y coordinate
            $table->timestamps(); // Laravel timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('anchors'); // Drop the anchors table
    }
};
