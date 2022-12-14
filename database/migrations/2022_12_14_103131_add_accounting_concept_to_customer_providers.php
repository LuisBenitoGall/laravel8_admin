<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountingConceptToCustomerProviders extends Migration{
    /**
     * Run the migrations.
     *
     * Campo de configuración de proveedor.
     *
     * @return void
     */
    public function up(){
        Schema::table('customer_providers', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('accounting_concept_id')->nullable()->after('syncro_invoice');
            $table->foreign('accounting_concept_id')->references('id')->on('accounting_concepts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('customer_providers', function (Blueprint $table){
            $table->dropColumn(['accounting_concept_id']);
        });
    }
}
