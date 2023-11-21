<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemLinkedOrdersTable extends Migration{
    /**
     * Run the migrations.
     *
     * Registro de items de pedidos vinculados a terceros pedidos.
     *
     * @return void
     */
    public function up(){
        Schema::create('item_linked_orders', function (Blueprint $table) {
            $table->id();
            //FK:
            $table->unsignedBigInteger('item_id')->nullable();
            $table->foreign('item_id')->references('id')->on('items');
            //FK:
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders');
            //FK:
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
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
        Schema::dropIfExists('item_linked_orders');
    }
}
