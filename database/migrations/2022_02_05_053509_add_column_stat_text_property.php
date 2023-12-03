<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStatTextProperty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('is_statistic');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->unsignedInteger('is_statistic')->default(0);
            $table->string('statistic_text')->nullable();
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
            $table->dropColumn('statistic_text');
            $table->dropColumn('is_statistic');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('is_statistic')->default(false);
        });
    }
}
