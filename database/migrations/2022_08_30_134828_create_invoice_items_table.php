<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceItemsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('invoice_items', function (Blueprint $table){
            $table->id();
            //FK:
            $table->bigInteger('invoice_id')->unsigned()->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices');
            //FK:
            $table->bigInteger('item_id')->unsigned()->nullable();
            $table->foreign('item_id')->references('id')->on('items');
            $table->float('qty',10,2)->default(0);
            $table->float('price',10,2)->default(0);
            $table->float('discount',5,2)->default(0);
            $table->integer('days')->default(0);
            $table->boolean('days_continue')->default(false);
            $table->float('days_continue_discount',5,2)->nullable();
            $table->float('amount',10,2)->default(0);
            $table->float('iva',4,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('invoice_items');
    }
}
