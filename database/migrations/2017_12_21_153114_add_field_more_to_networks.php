<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldMoreToNetworks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('networks', function (Blueprint $table) {
            $table->dropColumn([
                'api_url',
                'type'
            ]);

            $table->unsignedSmallInteger('rate_offer')->default(0);
            $table->unsignedSmallInteger('virtual_click')->default(0);
            $table->unsignedSmallInteger('virtual_lead')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('networks', function (Blueprint $table) {
            $table->dropColumn([
                'rate_offer',
                'virtual_click',
                'virtual_lead',
            ]);

            $table->string('api_url')->nullable();
            $table->string('type')->nullable();
        });
    }
}
