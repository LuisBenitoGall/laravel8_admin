<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('shipments', function (Blueprint $table){
            $table->id();
            $table->string('shipment_num')->nullable();
            $table->string('num_cardinal')->nullable();
            //FK:
            $table->unsignedBigInteger('pattern_id')->nullable();
            $table->foreign('pattern_id')->references('id')->on('shipment_patterns');
            $table->string('shipment_old_num')->nullable();
            //FK:
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
            $table->date('scheduled_date')->nullable();
            $table->longText('observations')->nullable();
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
        Schema::dropIfExists('shipments');
    }
}
