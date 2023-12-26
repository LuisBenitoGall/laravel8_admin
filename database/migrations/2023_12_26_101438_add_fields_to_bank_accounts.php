<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToBankAccounts extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('bank_accounts', function (Blueprint $table){
            $table->char('iban', 4)->nullable()->after('account');
            $table->char('entity', 4)->nullable()->after('iban');
            $table->char('office', 4)->nullable()->after('entity');
            $table->char('dc', 2)->nullable()->after('office');
            $table->char('digits', 10)->nullable()->after('dc');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('bank_accounts', function (Blueprint $table){
            $table->dropColumn(['iban', 'entity', 'office', 'dc', 'digits']);
        });
    }
}
