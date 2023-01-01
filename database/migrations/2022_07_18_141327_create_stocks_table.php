<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('stocks', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products');
            //FK:
            $table->unsignedBigInteger('stock_mov_id')->nullable();
            $table->foreign('stock_mov_id')->references('id')->on('stock_movements');
            //FK:
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->foreign('batch_id')->references('id')->on('batches');
            //FK:
            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores');
            //FK:
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->float('qty',11,2)->default(0);
            $table->char('operation', 1);     //Indica si la acción es de suma (s) o resta (r).
            $table->float('result',11,2)->default(0);
            $table->string('doc')->nullable();
            $table->string('doc_num')->nullable();
            //FK:
            $table->unsignedBigInteger('for_user')->nullable();
            $table->foreign('for_user')->references('id')->on('users');         //Usuario receptor de la línea de stock. Para entregas de consumo interno.
            $table->dateTime('accepted_at')->nullable();
            $table->dateTime('returned_at')->nullable();
            //FK:
            $table->unsignedBigInteger('product_serial_id')->nullable();
            $table->foreign('product_serial_id')->references('id')->on('product_serials');
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
        Schema::dropIfExists('stocks');
    }
}
