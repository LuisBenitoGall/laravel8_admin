<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinishedAtToOrders extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('orders', function (Blueprint $table){
            $table->dateTime('finished_at')->nullable()->after('order_status');
            //FK:
            $table->unsignedBigInteger('finished_by')->nullable()->after('finished_at');
            $table->foreign('finished_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('orders', function (Blueprint $table){
            $table->dropColumn(['finished_at', 'finished_by']);
        });
    }
}
