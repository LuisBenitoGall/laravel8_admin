<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCostCenterIdToItems extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('items', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('provider_cost_center_id')->nullable()->after('days_continue_discount');
            $table->foreign('provider_cost_center_id')->references('id')->on('cost_centers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('items', function (Blueprint $table){
            $table->dropColumn(['provider_cost_center_id']);
        });
    }
}
