<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_currencies', function (Blueprint $table) {
            $table->id(); // 'id' column as big unsigned integer (primary key)
            $table->string('client_code', 10)->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->tinyInteger('is_primary')->default(0)->comment('1 for yes, 0 for no');
            $table->decimal('doller_compare', 14, 8)->nullable(); // Using 'decimal' for 'doller_compare'
            $table->timestamps(); // 'created_at' and 'updated_at' columns (nullable)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_currencies');
    }
}
