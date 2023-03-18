<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProviderBankAccountIdToPayments extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('payments', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('provider_bank_account_id')->nullable()->after('bank_account_id');
            $table->foreign('provider_bank_account_id')->references('id')->on('bank_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('payments', function (Blueprint $table){
            $table->dropColumn(['provider_bank_account_id']);
        });
    }
}
