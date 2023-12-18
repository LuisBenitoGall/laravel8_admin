<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountingAccountIdToPurchaseProducts extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('purchase_products', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('accounting_account_id')->nullable()->after('iva');
            $table->foreign('accounting_account_id')->references('id')->on('accounting_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('purchase_products', function (Blueprint $table){
            $table->dropColumn(['accounting_account_id']);
        });
    }
}
