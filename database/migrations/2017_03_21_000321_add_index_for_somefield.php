<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexForSomefield extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clicks', function (Blueprint $table) {
            $table->index('hash_tag');
        });
        Schema::table('network_clicks', function (Blueprint $table) {
            $table->index('sub_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clicks', function (Blueprint $table) {
            $table->dropIndex('hash_tag');
        });
        Schema::table('network_clicks', function (Blueprint $table) {
            $table->dropIndex('sub_id');
        });
    }
}
