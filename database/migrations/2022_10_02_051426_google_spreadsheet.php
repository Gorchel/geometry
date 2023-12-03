<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GoogleSpreadSheet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_spread_sheet', function (Blueprint $table) {
            $table->increments('id');
            $table->string('spreadsheet_id');
            $table->string('spreadsheet_name');
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
        Schema::dropIfExists('google_spread_sheet');
    }
}
