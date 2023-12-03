<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RestAppBridge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rest_app_bridge', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('property_id');
            $table->unsignedInteger('type');
            $table->string('link')->unique();
            $table->longText('json');
            $table->string('hash');
            $table->softDeletes();
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
        Schema::dropIfExists('rest_app_bridge');
    }
}
