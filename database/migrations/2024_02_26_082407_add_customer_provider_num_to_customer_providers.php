<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerProviderNumToCustomerProviders extends Migration{
    /**
     * Run the migrations.
     *
     * Números customizados de cliente y proveedor.
     *
     * @return void
     */
    public function up(){
        Schema::table('customer_providers', function (Blueprint $table) {
            $table->string('customer_num')->nullable()->after('customer_id');   //El número de cliente que genera el proveedor.
            $table->string('provider_num')->nullable()->after('provider_id');   //El número de proveedor que genera el cliente.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('customer_providers', function (Blueprint $table) {
            $table->dropColumn(['customer_num', 'provider_num']);
        });
    }
}
