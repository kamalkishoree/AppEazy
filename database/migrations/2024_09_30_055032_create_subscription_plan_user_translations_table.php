<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPlanUserTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_plan_user_translations', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150)->nullable();
            $table->string('description', 255)->nullable();
            $table->bigInteger('subsplan_userid')->unsigned()->nullable();
            $table->bigInteger('language_id')->unsigned()->nullable();
            $table->foreign('subsplan_userid')->references('id')->on('subscription_plans_user')->onDelete('cascade');
            $table->foreign('language_id')->references('language_id')->on('client_languages')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_plan_user_translations');
    }
}
