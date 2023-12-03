<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GooglePropertyDropForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_property_sheet', function (Blueprint $table) {
            $table->dropForeign('google_property_sheet_property_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_property_sheet', function (Blueprint $table) {
            $table->foreign('property_id')->references('id')->on('properties');
        });
    }
}
