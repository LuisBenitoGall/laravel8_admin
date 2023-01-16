<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToTempImportCompanies extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('temp_import_companies', function (Blueprint $table){
            $table->string('iban')->nullable()->after('payday');
            $table->string('condicion_pago')->nullable()->after('iban');
            $table->string('concepto_general')->nullable()->after('condicion_pago');
            $table->string('cuenta_gastos')->nullable()->after('concepto_general');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('temp_import_companies', function (Blueprint $table){
            $table->dropColumn(['iban', 'condicion_pago', 'concepto_general', 'cuenta_gastos']);
        });
    }
}
