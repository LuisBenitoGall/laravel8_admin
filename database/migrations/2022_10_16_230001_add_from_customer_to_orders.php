<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFromCustomerToOrders extends Migration{
    /**
     * Run the migrations.
     *
     * Indica si el pedido proviene de una factura introducida directamente por el cliente. Para casos en que no existe el pedido en el sistema y se introduce una factura. En esta situación se genera factura, su pedido y este campo indica si se debe permitir al cliente introducir las líneas del pedido.
     *
     * @return void
     */
    public function up(){
        Schema::table('orders', function (Blueprint $table){
            $table->boolean('from_customer')->default(false)->after('recid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('orders', function (Blueprint $table){
            $table->dropColumn(['from_customer']);
        });
    }
}
