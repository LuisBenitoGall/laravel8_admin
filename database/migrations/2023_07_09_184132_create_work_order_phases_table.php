<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkOrderPhasesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('work_order_phases', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('work_order_id')->nullable();
            $table->foreign('work_order_id')->references('id')->on('work_orders');
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->longText('description')->nullable();
            $table->integer('sort')->nullable();
            //FK:
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
            $table->boolean('is_reference')->default(false);    //Indica si se debe utilizar como referencia -copiar- para otras órdenes de trabajo del mismo producto.
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
        Schema::dropIfExists('work_order_phases');
    }
}
