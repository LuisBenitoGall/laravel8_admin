<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStoreIdToWorkOrderProducts extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('work_order_products', function (Blueprint $table) {
            //FK:
            $table->unsignedBigInteger('store_id')->nullable()->after('work_order_id');
            $table->foreign('store_id')->references('id')->on('stores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('work_order_products', function (Blueprint $table) {
            $table->dropColumn(['store_id']);
        });
    }
}
