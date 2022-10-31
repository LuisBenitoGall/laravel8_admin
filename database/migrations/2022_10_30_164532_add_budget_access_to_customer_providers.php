<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBudgetAccessToCustomerProviders extends Migration{
    /**
     * Run the migrations.
     *
     * Permitir acceso por defecto del cliente a los presupuestos del proveedor.
     *
     * @return void
     */
    public function up(){
        Schema::table('customer_providers', function (Blueprint $table){
            $table->boolean('budget_access')->default(false)->after('budget_form_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('customer_providers', function (Blueprint $table){
            $table->dropColumn(['budget_access']);
        });
    }
}
