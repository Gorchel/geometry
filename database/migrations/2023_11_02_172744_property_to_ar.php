<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PropertyToAr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_to_ar', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('property_id');
            $table->unsignedInteger('ar_id');
            $table->text('response');
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
        Schema::dropIfExists('property_to_ar');
    }
}
