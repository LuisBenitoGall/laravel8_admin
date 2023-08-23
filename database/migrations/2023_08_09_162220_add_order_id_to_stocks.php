<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderIdToStocks extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('stocks', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('order_id')->nullable()->after('stock_mov_id');
            $table->foreign('order_id')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('stocks', function (Blueprint $table){
            $table->dropColumn(['order_id']);
        });
    }
}
