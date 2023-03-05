<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('payments', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('company_from')->nullable();
            $table->foreign('company_from')->references('id')->on('companies');
            //FK:
            $table->unsignedBigInteger('company_to')->nullable();
            $table->foreign('company_to')->references('id')->on('companies');
            $table->float('amount',16,2)->default(0);
            $table->date('payment_date')->nullable();
            //FK:
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
            //FK:
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices');
            //FK:
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts');
            $table->boolean('is_ticket')->default(false);       //Si existe factura, el ticket es siempre false;
            $table->longText('observations')->nullable();
            //FK:
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('payments');
    }
}
