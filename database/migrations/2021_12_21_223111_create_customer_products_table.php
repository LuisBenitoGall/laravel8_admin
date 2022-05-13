<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerProductsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('customer_products', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products');
             //FK:
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->foreign('provider_id')->references('id')->on('companies');
            //FK:
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('companies');
            //FK:
            $table->unsignedBigInteger('ba_id')->nullable();
            $table->foreign('ba_id')->references('id')->on('business_areas');
            //FK:
            $table->unsignedBigInteger('cc_id')->nullable();
            $table->foreign('cc_id')->references('id')->on('cost_centers');
            $table->float('price',5,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('customer_products');
    }
}
