<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSatsTable extends Migration{
    /**
     * Run the migrations.
     *
     * Se generan 2 pedidos: 
     * 1. Venta del servicio al cliente final.
     * 2. Compra del servicio a la empresa que realiza el servicio. En esta debe constar el contratante como cliente final.
     *
     * @return void
     */
    public function up(){
        Schema::create('sats', function (Blueprint $table) {
            $table->id();
            $table->string('sat_num')->nullable();
            $table->string('sat_num_cardinal')->nullable();
            //FK:
            $table->unsignedBigInteger('sat_pattern_id')->nullable();
            $table->foreign('sat_pattern_id')->references('id')->on('sat_patterns');
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            //FK:
            $table->unsignedBigInteger('company_id')->nullable();           //Empresa que factura a cliente.
            $table->foreign('company_id')->references('id')->on('companies');
            //FK:
            $table->unsignedBigInteger('contractor_id')->nullable();        //Empresa que contrata el servicio.
            $table->foreign('contractor_id')->references('id')->on('companies');
            //FK, orden de venta:
            $table->unsignedBigInteger('sale_id')->nullable();       
            $table->foreign('sale_id')->references('id')->on('orders');
            //FK, orden de compra a la subcontrata:
            $table->unsignedBigInteger('purchase_id')->nullable();       
            $table->foreign('purchase_id')->references('id')->on('orders');
            //FK:
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('sats');
    }
}
