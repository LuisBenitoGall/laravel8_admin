<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('vehicles', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('name');
            $table->integer('vehicle_type')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('car_license')->nullable();
            $table->date('discharge_date')->nullable();                 //Fecha de alta.
            $table->float('vehicle_base_weight', 9,2)->nullable();      //Peso en kgs.
            $table->float('max_weight', 9,2)->nullable();           //Peso en kgs.
            $table->string('chassis_number')->nullable();
            $table->longText('observations')->nullable();
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
        Schema::dropIfExists('vehicles');
    }
}
