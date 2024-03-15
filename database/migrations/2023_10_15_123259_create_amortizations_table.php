<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmortizationsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('amortizations', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('company_id');                       
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('code')->nullable();
            $table->longText('description')->nullable();
            $table->longText('permission')->nullable();
            $table->date('date')->nullable();
            $table->float('amount',10,2)->default(0);
            //FK:
            $table->unsignedBigInteger('amortization_account')->nullable();
            $table->foreign('amortization_account')->references('id')->on('accounting_accounts');
            //FK:
            $table->unsignedBigInteger('amortization_dota')->nullable();
            $table->foreign('amortization_dota')->references('id')->on('accounting_accounts');
            $table->integer('year')->nullable();
            $table->integer('month')->nullable();
            $table->float('amortization_pendent',10,2)->default(0);
            //FK:
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
            $table->integer('status')->nullable();
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
        Schema::dropIfExists('amortizations');
    }
}
