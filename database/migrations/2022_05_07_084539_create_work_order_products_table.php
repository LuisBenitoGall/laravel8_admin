<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkOrderProductsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('work_order_products', function (Blueprint $table) {
            $table->id();
            //FK:
            $table->unsignedBigInteger('work_order_id')->nullable();
            $table->foreign('work_order_id')->references('id')->on('work_orders');
            //FK:
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products');
            $table->float('planned_qty',10,2)->default(0);
            $table->float('produced_qty',10,2)->default(0);
            $table->longText('observations')->nullable();
            $table->boolean('with_batch')->default(false);
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
        Schema::dropIfExists('work_order_products');
    }
}
