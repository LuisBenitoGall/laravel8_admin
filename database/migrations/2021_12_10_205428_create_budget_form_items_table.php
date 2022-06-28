<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetFormItemsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('budget_form_items', function (Blueprint $table){
            $table->id();
            $table->bigInteger('recid')->nullable();
            //FK:
            $table->unsignedBigInteger('form_id')->nullable();
            $table->foreign('form_id')->references('id')->on('budget_forms');
            //FK: para los bloques que se incluyan exclusivamente en un presupuesto pero NO en su formulario padre:
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders');
            //FK:
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products');
            $table->text('name')->nullable();
            $table->text('translations')->nullable();
            $table->text('observations')->nullable();
            $table->float('discount', 5, 2)->nullable();
            $table->float('qty_min',11,2)->nullable();
            $table->float('calculated_by_percent', 5, 2)->nullable();
            $table->boolean('fixed_in_budget')->default(0);
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
        Schema::dropIfExists('budget_form_items');
    }
}
