<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentDeliveriesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('shipment_deliveries', function (Blueprint $table) {
            $table->id();
            //FK:
            $table->unsignedBigInteger('shipment_id')->nullable();
            $table->foreign('shipment_id')->references('id')->on('shipments');
            //FK:
            $table->unsignedBigInteger('delivery_id')->nullable();
            $table->foreign('delivery_id')->references('id')->on('deliveries');
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('shipment_deliveries');
    }
}
