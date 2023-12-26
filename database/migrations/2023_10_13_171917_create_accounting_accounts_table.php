<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountingAccountsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('accounting_accounts', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('company_id');                       //Empresa titular de la cuenta.
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('account');
            $table->string('name');
            $table->char('type', 3)->nullable(); 
            //FK:
            $table->unsignedBigInteger('company_target')->nullable();       //Empresa sobre la que se aplica la cuenta.       
            $table->foreign('company_target')->references('id')->on('companies');
            $table->integer('status'); 
            $table->integer('featured')->default('0');
            $table->boolean('with_iva')->default(true);
            //FK:
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');  
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
        Schema::dropIfExists('accounting_accounts');
    }
}
