<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ParsingQueueStatusUpdated extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parsing_queue', function (Blueprint $table) {
            $table->integer('status_updated_at')->nullable();
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
            $table->dropColumn('status_updated_at');
        });
    }
}
