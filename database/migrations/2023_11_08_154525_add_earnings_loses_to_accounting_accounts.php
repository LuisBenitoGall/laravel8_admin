<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEarningsLosesToAccountingAccounts extends Migration{
    /**
     * Run the migrations.
     *
     * Indica si la cuenta es de ganancias o pérdidas para los tipos de cuenta 6 y 7
     *
     * @return void
     */
    public function up(){
        Schema::table('accounting_accounts', function (Blueprint $table) {
            $table->boolean('earnings_loses')->default(false)->after('aatype_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('accounting_accounts', function (Blueprint $table) {
            $table->dropColumn(['earnings_loses']);
        });
    }
}
