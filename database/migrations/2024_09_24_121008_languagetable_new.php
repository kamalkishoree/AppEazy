<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LanguagetableNew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto-incrementing UNSIGNED BIGINT (Primary Key)
            $table->string('sort_code', 4)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('name', 100)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('nativeName', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->timestamps(); // This will create `created_at` and `updated_at` columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
