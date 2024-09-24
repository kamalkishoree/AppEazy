<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientLanguagesTable extends Migration
{
    public function up()
    {
        Schema::create('client_languages', function (Blueprint $table) {
            $table->string('client_code', 10)->nullable();
            $table->unsignedBigInteger('language_id')->nullable();
            $table->tinyInteger('is_primary')->default(0)->comment('1 for yes, 0 for no');
            $table->tinyInteger('is_active')->default(0)->comment('1 for yes, 0 for no');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_languages');
    }
    
}
