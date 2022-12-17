<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountingConceptoToInvoices extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('invoices', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('accounting_structure_id')->nullable()->after('customer_observations');
            $table->foreign('accounting_structure_id')->references('id')->on('accounting_concepts');
            //FK:
            $table->unsignedBigInteger('accounting_concept_id')->nullable()->after('accounting_structure_id');
            $table->foreign('accounting_concept_id')->references('id')->on('accounting_concepts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('invoices', function (Blueprint $table){
            $table->dropColumn(['accounting_structure_id', 'accounting_concept_id']);
        });
    }
}
