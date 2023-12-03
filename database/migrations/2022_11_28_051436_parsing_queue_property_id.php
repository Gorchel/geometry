<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ParsingQueuePropertyId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parsing_queue', function (Blueprint $table) {
            $table->integer('property_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parsing_queue', function (Blueprint $table) {
            $table->dropColumn('property_id');
        });
    }
}
