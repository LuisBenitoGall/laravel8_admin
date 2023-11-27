<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountPriorityToInvoices extends Migration{
    /**
     * Run the migrations.
     *
     * Determina la prioridad para fija el importe de la factura: si es por importe neto (1) o por porcentaje (2);
     *
     * @return void
     */
    public function up(){
        Schema::table('invoices', function (Blueprint $table) {
            $table->char('amount_priority', 1)->default(1)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['amount_priority']);
        });
    }
}
