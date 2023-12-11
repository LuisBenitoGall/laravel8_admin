<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountingAccountYearBalancesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('accounting_account_year_balances', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('accounting_account_id');
            $table->foreign('accounting_account_id')->references('id')->on('accounting_accounts');
            $table->char('year', 4)->nullable();
            $table->float('balance', 11, 2)->default(0);
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('accounting_account_year_balances');
    }
}
