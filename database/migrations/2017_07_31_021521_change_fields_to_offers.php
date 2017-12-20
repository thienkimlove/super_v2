<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldsToOffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('virtual_clicks');
            $table->unsignedInteger('number_when_click')->default(0);
            $table->unsignedInteger('number_when_lead')->default(0);
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
            $table->boolean('virtual_clicks')->default(false);
            $table->dropColumn(['number_when_click', 'number_when_click']);
        });
    }
}
