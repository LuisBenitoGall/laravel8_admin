<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('batches', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products');
            //FK:
            $table->unsignedBigInteger('work_order_id')->nullable();
            $table->foreign('work_order_id')->references('id')->on('work_orders');
            $table->string('name')->nullable();
            $table->date('deadline')->nullable();        //Fecha cierre de lote
            $table->date('expiry_date')->nullable();     //Fecha caducidad del lote
            $table->float('initial_qty',11,2)->default(0);
            $table->string('doc_num', 50)->nullable();
            $table->string('doc')->nullable();
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
        Schema::dropIfExists('batches');
    }
}
