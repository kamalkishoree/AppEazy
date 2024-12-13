<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTbleSubsciptionInvoiceUser extends Migration
{
    public function up()
    {
        Schema::table('subscription_invoices_user', function (Blueprint $table) {
            $table->integer('subscription_type')->default(0)
                  ->comment('subscription_type: 0 = admin, 1 = vendor');
      
        });
    }

    public function down()
    {
        Schema::table('subscription_invoices_user', function (Blueprint $table) {
            $table->dropColumn('subscription_type');
        });
    }
}
