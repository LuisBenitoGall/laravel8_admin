<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrepareDeliveryToOrders extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('orders', function (Blueprint $table){
            $table->integer('prepare_delivery')->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('orders', function (Blueprint $table){
            $table->dropColumn(['prepare_delivery']);
        });
    }
}
