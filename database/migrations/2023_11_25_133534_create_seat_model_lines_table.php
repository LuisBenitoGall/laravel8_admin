<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatModelLinesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('seat_model_lines', function (Blueprint $table) {
            $table->id();
            //FK:
            $table->unsignedBigInteger('model_id')->nullable();
            $table->foreign('model_id')->references('id')->on('seat_models');
            //FK:
            $table->unsignedBigInteger('accounting_account_id')->nullable();
            $table->foreign('accounting_account_id')->references('id')->on('accounting_accounts');
            $table->integer('line_num')->nullable();
            $table->float('debit',12,2)->nullable();
            $table->float('credit',12,2)->nullable();
            $table->string('code_auto')->nullable();
            $table->string('type')->nullable();
            //FK:
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->foreign('currency_id')->references('id')->on('currencies');
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
        Schema::dropIfExists('seat_model_lines');
    }
}
