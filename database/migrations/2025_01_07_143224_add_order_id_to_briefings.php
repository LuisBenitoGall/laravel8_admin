<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderIdToBriefings extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('briefings', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('order_id')->nullable()->after('template_id');
            $table->foreign('order_id')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('briefings', function (Blueprint $table){
            $table->dropColumn(['order_id']);
        });
    }
}
