<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemittancesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('remittances', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            //FK:
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts');
            $table->string('remittance_num')->nullable();
            $table->string('num_cardinal')->nullable();
            //FK:
            $table->unsignedBigInteger('pattern_id')->nullable();
            $table->foreign('pattern_id')->references('id')->on('remittance_patterns');
            $table->string('remittance_old_num')->nullable();
            $table->float('amount',12,2)->default(0);
            //FK:
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->dateTime('send_date')->nullable();
            $table->dateTime('paying_date')->nullable();
            //FK:
            $table->unsignedBigInteger('paying_by')->nullable();
            $table->foreign('paying_by')->references('id')->on('users');
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
        Schema::dropIfExists('remittances');
    }
}
