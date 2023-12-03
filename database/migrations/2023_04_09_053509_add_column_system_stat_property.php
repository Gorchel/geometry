<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSystemStatProperty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->unsignedInteger('system_statistic')->default(0);;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('system_statistic');
        });
    }
}
