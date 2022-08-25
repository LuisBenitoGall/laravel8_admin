<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidenceLinesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('incidence_lines', function (Blueprint $table){
            $table->id();
            //FK:
            $table->bigInteger('incidence_id')->unsigned()->nullable();
            $table->foreign('incidence_id')->references('id')->on('incidences');
            //FK:
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products');
            $table->float('qty',10,2)->default(0);
            //FK:
            $table->bigInteger('batch_id')->unsigned()->nullable();
            $table->foreign('batch_id')->references('id')->on('batches');
            //FK:
            $table->bigInteger('product_serial_id')->unsigned()->nullable();
            $table->foreign('product_serial_id')->references('id')->on('product_serials');
            $table->longText('observations')->nullable();
            $table->integer('status')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incidence_lines');
    }
}
