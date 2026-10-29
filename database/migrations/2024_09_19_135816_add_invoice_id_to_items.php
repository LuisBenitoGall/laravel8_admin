<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceIdToItems extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('items', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('invoice_id')->nullable()->after('calculated_by_percent');
            $table->foreign('invoice_id')->references('id')->on('invoices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('items', function (Blueprint $table){
            $table->dropColumn(['invoice_id']);
        });
    }
}
