<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBankAccountToCustomerProviders extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('customer_providers', function (Blueprint $table){
            //FK: se incluye la cuenta bancaria del proveedor.
            $table->unsignedBigInteger('bank_account_id')->nullable()->after('accounting_concept_id');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('customer_providers', function (Blueprint $table){
            $table->dropColumn(['bank_account_id']);
        });
    }
}
