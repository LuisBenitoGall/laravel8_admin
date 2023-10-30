<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModeToAccountingAccountTypes extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('accounting_account_types', function (Blueprint $table){
            $table->char('mode', 2)->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('accounting_account_types', function (Blueprint $table){
            $table->dropColumn(['mode']);
        });
    }
}
