<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentMethodToCustomerProviders extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('customer_providers', function (Blueprint $table){
            $table->integer('payment_method')->nullable()->after('payday');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('customer_providers', function (Blueprint $table){
            $table->dropColumn(['payment_method']);
        });
    }
}
