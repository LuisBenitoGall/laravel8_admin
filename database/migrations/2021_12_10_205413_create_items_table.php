<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('items', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products');
            //FK:
            $table->unsignedBigInteger('budget_order_id')->nullable();
            $table->foreign('budget_order_id')->references('id')->on('budget_order_shared_fields');
            //FK:
            $table->unsignedBigInteger('budget_form_block_id')->nullable();
            $table->foreign('budget_form_block_id')->references('id')->on('budget_form_items');
            //FK:
            $table->unsignedBigInteger('budget_form_item_id')->nullable();
            $table->foreign('budget_form_item_id')->references('id')->on('budget_form_items');

            $table->integer('budget_order')->nullable();
            $table->text('concept')->nullable();
            $table->text('observations')->nullable();
            $table->float('qty',10,2)->default(0);
            $table->float('price',10,2)->default(0);
            $table->string('item_no_price')->nullable();   //Indica el texto a mostrar en la columna de precio para las líneas con cantidad > 0 y precio = 0;
            $table->float('iva',4,2)->nullable();
            $table->float('discount',5,2)->nullable();
            $table->float('calculated_by_percent',8,2)->nullable();
            $table->timestamp('invoiced')->nullable();
            $table->timestamp('deposit_used')->nullable();
            $table->integer('days')->default(0);
            $table->boolean('days_continue')->default(false);
            $table->float('days_continue_discount',5,2)->nullable();
            $table->integer('active')->default(1);
            $table->integer('status')->nullable();
            $table->boolean('print')->default(true);
            //FK:
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('items');
    }
}
