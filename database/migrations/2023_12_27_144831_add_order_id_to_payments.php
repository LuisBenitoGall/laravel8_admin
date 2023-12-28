<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderIdToPayments extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('payments', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('order_id')->nullable()->after('amount');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->boolean('is_pay_sign')->default(false)->after('is_ticket');         //Si es paga y señal.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('payments', function (Blueprint $table){
            $table->dropColumn(['order_id', 'is_pay_sign']);
        });
    }
}
