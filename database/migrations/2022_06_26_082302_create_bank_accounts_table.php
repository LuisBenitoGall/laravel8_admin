<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('bank_accounts', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            //FK:
            $table->unsignedBigInteger('bank_id');
            $table->foreign('bank_id')->references('id')->on('banks');
            $table->string('account');
            $table->boolean('featured')->default(false);
            $table->boolean('status')->default(false);
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
        Schema::dropIfExists('bank_accounts');
    }
}
