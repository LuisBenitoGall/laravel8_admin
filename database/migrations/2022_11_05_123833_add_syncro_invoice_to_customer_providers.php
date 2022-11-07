<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSyncroInvoiceToCustomerProviders extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('customer_providers', function (Blueprint $table){
            $table->boolean('syncro_invoice')->default(false)->after('budget_access');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('customer_providers', function (Blueprint $table){
            $table->dropColumn(['syncro_invoice']);
        });
    }
}
