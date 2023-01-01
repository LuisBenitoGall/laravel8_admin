<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyAccountsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('company_accounts', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            //FK: empresa tutora que gestiona a la empresa como cliente o proveedor mientras no tenga responsable propio:
            $table->unsignedBigInteger('guardian')->nullable();
            $table->foreign('guardian')->references('id')->on('companies');
            //FK:
            $table->unsignedBigInteger('account_id');
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('price', 9, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->integer('status')->default(1);      //Leyenda: 0 = inactivo; 1 = activo; 2 = caducado.
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
        Schema::dropIfExists('company_accounts');
    }
}
