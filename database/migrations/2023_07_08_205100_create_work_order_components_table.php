<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkOrderComponentsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('work_order_components', function (Blueprint $table) {
            $table->id();
            //FK:
            $table->unsignedBigInteger('work_order_id')->nullable();
            $table->foreign('work_order_id')->references('id')->on('work_orders');
            //FK:
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products');
            $table->float('supplied_qty',12,2)->default(0);
            $table->float('used_qty',12,2)->nullable();
            $table->float('returned_qty',12,2)->nullable();
            //FK:
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->foreign('unit_id')->references('id')->on('units');
            //FK:
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
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
        Schema::dropIfExists('work_order_components');
    }
}
