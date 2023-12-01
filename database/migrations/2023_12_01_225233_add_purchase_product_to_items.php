<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseProductToItems extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('items', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('purchase_product_id')->nullable()->after('customer_product_id');
            $table->foreign('purchase_product_id')->references('id')->on('purchase_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('items', function (Blueprint $table){
            $table->dropColumn(['purchase_product_id']);
        });
    }
}
