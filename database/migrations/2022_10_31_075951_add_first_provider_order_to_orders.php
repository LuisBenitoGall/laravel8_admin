<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFirstProviderOrderToOrders extends Migration{
    /**
     * Run the migrations.
     *
     * Guarda el pedido del proveedor inicial para pedidos con cliente final.
     *
     * @return void
     */
    public function up(){
        Schema::table('orders', function (Blueprint $table){
            $table->bigInteger('first_provider_order')->nullable()->after('final_customer_manager_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('orders', function (Blueprint $table){
            $table->dropColumn(['first_provider_order']);
        });
    }
}
