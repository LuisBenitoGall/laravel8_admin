<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerConfirmationToOrders extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('orders', function (Blueprint $table){
            $table->timestamp('customer_confirmation')->nullable()->after('customer_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('orders', function (Blueprint $table){
            $table->dropColumn(['customer_confirmation']);
        });
    }
}
