<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableVirtualLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtual_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('offer_id')->index();
            $table->unsignedInteger('click_id')->nullable();
            $table->unsignedInteger('network_click_id')->nullable();
            $table->string('user_country')->nullable();
            $table->string('user_agent')->nullable();
            $table->longText('response')->nullable();
            $table->boolean('sent')->default(false);
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
        Schema::dropIfExists('virtual_logs');
    }
}
