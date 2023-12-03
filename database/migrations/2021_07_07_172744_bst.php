<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Bst extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bst_bridge', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('property_id');
            $table->unsignedInteger('bst_id');
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
        Schema::dropIfExists('bst_bridge');
    }
}
