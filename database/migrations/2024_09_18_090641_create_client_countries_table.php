<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_countries', function (Blueprint $table) {
            $table->id(); // Primary key as a big unsigned integer
            $table->string('client_code', 10)->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->tinyInteger('is_primary')->default(0)->comment('1 for yes, 0 for no');
            $table->tinyInteger('is_active')->default(0)->comment('1 for yes, 0 for no');
            $table->timestamps(); // Creates `created_at` and `updated_at` columns, both nullable by default
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_countries');
    }
}
