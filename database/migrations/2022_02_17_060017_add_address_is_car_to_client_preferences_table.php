<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressIsCarToClientPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_preferences', function (Blueprint $table) {
             $table->tinyInteger('address_is_car')->nullable()->default(0)->comment('0-No, 1-Yes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_preferences', function (Blueprint $table) {
            //
        });
    }
}
