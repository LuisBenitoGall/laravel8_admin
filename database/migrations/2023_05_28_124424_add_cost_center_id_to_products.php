<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCostCenterIdToProducts extends Migration{
    /**
     * Run the migrations.
     *
     * Los productos pueden pertenecer a un Centro de Coste, para poder asignar las ventas de ese producto a su correspondiente Centro de Coste.
     *
     * @return void
     */
    public function up(){
        Schema::table('products', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('cost_center_id')->nullable()->after('on_sale');
            $table->foreign('cost_center_id')->references('id')->on('cost_centers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('products', function (Blueprint $table){
            $table->dropColumn(['cost_center_id']);
        });
    }
}
