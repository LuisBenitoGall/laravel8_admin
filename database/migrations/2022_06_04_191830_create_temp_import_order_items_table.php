<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempImportOrderItemsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('temp_import_order_items', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('budget_order_id');
            $table->foreign('budget_order_id')->references('id')->on('budget_order_shared_fields');
            $table->string('ref')->nullable();
            $table->text('concept')->nullable();
            $table->float('qty',10,2)->default(0);
            $table->float('price',10,2)->default(0);
            $table->float('iva',4,2)->nullable();
            $table->float('discount',5,2)->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('temp_import_order_items');
    }
}
