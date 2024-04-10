<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTblVendorSlotsAddColumnCarRental extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_slots', function (Blueprint $table) {
            $table->tinyInteger('car_rental')->after('rental')->default(0)->comment('1 for yes, 0 for no');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_slots', function (Blueprint $table) {
            $table->dropColumn('car_rental');
        });
    }
}
