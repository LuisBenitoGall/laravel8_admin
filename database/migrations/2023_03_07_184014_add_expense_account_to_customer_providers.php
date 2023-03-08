<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpenseAccountToCustomerProviders extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('customer_providers', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('expense_account_id')->nullable()->after('accounting_concept_id');
            $table->foreign('expense_account_id')->references('id')->on('accounting_concepts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('customer_providers', function (Blueprint $table){
            $table->dropColumn(['expense_account_id']);
        });
    }
}
