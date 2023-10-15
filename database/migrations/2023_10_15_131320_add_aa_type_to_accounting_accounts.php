<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAaTypeToAccountingAccounts extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('accounting_accounts', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('aatype_id')->nullable()->after('type');
            $table->foreign('aatype_id')->references('id')->on('accounting_account_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('accounting_accounts', function (Blueprint $table){
            $table->dropColumn(['aatype_id']);
        });
    }
}
