<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldOfferIdClickIdToNetworkClicks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('network_clicks', function (Blueprint $table) {
            $table->unsignedInteger('offer_id')->nullable()->index();
            $table->unsignedInteger('click_id')->nullable()->index();
            $table->boolean('status')->default(true);
            $table->longText('json_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('network_clicks', function (Blueprint $table) {

            $table->dropIndex([
                'offer_id',
                'click_id',
            ]);

            $table->dropColumn([
                'offer_id',
                'click_id',
                'status',
                'json_data',
            ]);
        });
    }
}
