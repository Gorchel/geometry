<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GooglePropertySheet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_property_sheet', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('property_id');
            $table->integer('sheet_id');
            $table->string('sheet_name');
            $table->unsignedInteger('spreadsheet_id');
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('spreadsheet_id')->references('id')->on('google_spread_sheet');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('google_property_sheet');
    }
}
