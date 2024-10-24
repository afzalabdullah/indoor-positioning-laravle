<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropAssetsColumnFromSitesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('assets'); // Drop the assets column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->integer('assets')->default(0); // Add the assets column back
        });
    }
}
