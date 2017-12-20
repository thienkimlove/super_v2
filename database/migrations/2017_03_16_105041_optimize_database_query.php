<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OptimizeDatabaseQuery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->index('auto');
            $table->index('net_offer_id');
            $table->index('network_id');
        });

        Schema::table('network_clicks', function (Blueprint $table) {
            $table->index('network_id');
            $table->index('network_offer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropIndex('auto');
            $table->dropIndex('net_offer_id');
            $table->dropIndex('network_id');
        });

        Schema::table('network_clicks', function (Blueprint $table) {
            $table->dropIndex('network_id');
            $table->dropIndex('network_offer_id');
        });
    }
}
