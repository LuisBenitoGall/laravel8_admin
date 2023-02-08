<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBankAccountToInvoices extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('invoices', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('bank_account_id')->nullable()->after('template');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('invoices', function (Blueprint $table){
            $table->dropColumn(['bank_account_id']);
        });
    }
}
