<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEffectsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('effects', function (Blueprint $table){
            $table->id();
            $table->string('effect_num')->nullable();
            $table->boolean('is_group')->default(false);
            $table->bigInteger('parent')->nullable();   //Id de efecto padre que agrupa a efectos hijo
            $table->date('date');
            //FK:
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            //FK:
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->foreign('provider_id')->references('id')->on('companies');
            //FK:
            $table->unsignedBigInteger('provider_bank_account_id')->nullable();
            $table->foreign('provider_bank_account_id')->references('id')->on('bank_accounts');
            //FK:
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('companies');
            //FK:
            $table->unsignedBigInteger('customer_bank_account_id')->nullable();
            $table->foreign('customer_bank_account_id')->references('id')->on('bank_accounts');
            //FK:
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices');
            //FK:
            $table->unsignedBigInteger('remittance_id')->nullable();
            $table->foreign('remittance_id')->references('id')->on('remittances');
            //FK:
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
            $table->integer('payment_status')->nullable();
            $table->float('amount',12,2)->default(0);
            //FK:
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->longText('observations')->nullable();
            //FK:
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('effects');
    }
}
