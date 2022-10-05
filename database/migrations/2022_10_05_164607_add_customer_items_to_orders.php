<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerItemsToOrders extends Migration{
    /**
     * Run the migrations.
     *
     * Campo creado específicamente para Implant Novartis, donde guardar el tipo de documento.
     *
     * @return void
     */
    public function up(){
        Schema::table('orders', function (Blueprint $table){
            $table->text('customer_items')->nullable()->after('customer_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('orders', function (Blueprint $table){
            $table->dropColumn(['customer_items']);
        });
    }
}
