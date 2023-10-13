<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceDateToInvoices extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('invoices', function (Blueprint $table){
            $table->date('invoice_date')->nullable()->after('fiscal_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('invoices', function (Blueprint $table){
            $table->dropColumn(['invoice_date']);
        });
    }
}
