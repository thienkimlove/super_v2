<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldAllowDevicesToVirtualLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('virtual_logs', function (Blueprint $table) {
            $table->unsignedSmallInteger('allow_devices')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('virtual_logs', function (Blueprint $table) {
            $table->dropColumn('allow_devices');
        });
    }
}
