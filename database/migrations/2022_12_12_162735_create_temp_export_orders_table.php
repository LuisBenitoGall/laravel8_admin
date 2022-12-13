<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempExportOrdersTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('temp_export_orders', function (Blueprint $table){
            $table->id();
            $table->string('temp_num')->nullable();
            $table->string('order_date')->nullable();
            $table->string('order_num')->nullable();
            $table->string('order_old')->nullable();
            $table->string('order_provider_manager')->nullable();
            $table->string('order_customer')->nullable();
            $table->string('order_customer_manager')->nullable();
            $table->string('order_business_area')->nullable();
            $table->string('order_cost_center')->nullable();
            $table->string('order_tipology')->nullable();
            $table->float('order_price',10,2)->default(0);
            $table->float('order_qty',10,2)->default(0);
            $table->float('order_amount',10,2)->default(0);            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('temp_export_orders');
    }
}
