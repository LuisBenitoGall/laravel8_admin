<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIvaTypeToAccountingAccounts extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('accounting_accounts', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('iva_type_id')->nullable()->after('with_iva');
            $table->foreign('iva_type_id')->references('id')->on('iva_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('accounting_accounts', function (Blueprint $table){
            $table->dropColumn(['iva_type_id']);
        });
    }
}
