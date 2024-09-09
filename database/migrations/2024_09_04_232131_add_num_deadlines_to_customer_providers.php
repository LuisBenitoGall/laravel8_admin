<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumDeadlinesToCustomerProviders extends Migration{
    /**
     * Run the migrations.
     *
     * Establece el número de plazos para el pago de facturas, y por tanto para la generación de efectos.
     *
     * @return void
     */
    public function up(){
        Schema::table('customer_providers', function (Blueprint $table){
            $table->integer('num_deadlines')->default(1)->after('payment_method_id');
            $table->integer('days_between_deadlines')->nullable()->after('num_deadlines');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('customer_providers', function (Blueprint $table){
            $table->dropColumn(['num_deadlines', 'days_between_deadlines']);
        });
    }
}
