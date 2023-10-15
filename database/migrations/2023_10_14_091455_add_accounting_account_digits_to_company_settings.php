<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountingAccountDigitsToCompanySettings extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('company_settings', function (Blueprint $table){
            $table->integer('accounting_account_digits')->nullable()->after('recommendations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('company_settings', function (Blueprint $table){
            $table->dropColumn(['accounting_account_digits']);
        });
    }
}
