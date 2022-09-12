<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductProvidersTable extends Migration{
    /**
     * Run the migrations.
     * 
     * Registra la correspondencia entre una referencia de producto de cliente y los distintos proveedores de ese producto.
     *
     * @return void
     */
    public function up(){
        Schema::create('product_providers', function (Blueprint $table){
            $table->id();
            //FK:
            $table->bigInteger('customer_product_id')->unsigned();
            $table->foreign('customer_product_id')->references('id')->on('products');
            //FK:
            $table->bigInteger('provider_product_id')->unsigned()->nullable();
            $table->foreign('provider_product_id')->references('id')->on('products');
            //FK:
            $table->bigInteger('provider_id')->unsigned();
            $table->foreign('provider_id')->references('id')->on('companies');
            $table->string('provider_ref')->nullable();
            $table->text('provider_product')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('product_providers');
    }
}
