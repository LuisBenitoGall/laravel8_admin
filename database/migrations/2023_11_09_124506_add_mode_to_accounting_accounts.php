<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModeToAccountingAccounts extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('accounting_accounts', function (Blueprint $table){
            $table->integer('mode')->nullable()->after('name');     //Determina la modalidad de la cuenta. Leyenda: 1->clientes; 2->proveedores; 3->bancos; 4->contabilidad
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('accounting_accounts', function (Blueprint $table){
            $table->dropColumn(['mode']);
        });
    }
}
