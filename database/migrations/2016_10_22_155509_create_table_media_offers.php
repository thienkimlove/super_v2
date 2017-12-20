<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMediaOffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_offers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('offer_id')->unique();
            $table->text('offer_name');
            $table->string('offer_preview_link');
            $table->string('offer_tracking_link');
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
        Schema::dropIfExists('media_offers');
    }
}
