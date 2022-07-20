<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockMovementsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('stock_movements', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('name');
            $table->text('translations')->nullable();
            $table->char('acronym',3);
            $table->char('sign',1);     //Indica si la acción es de suma, resta o traspaso.
            $table->boolean('basic')->default(false);    //Indica si se trata de un movimiento por defecto o creado por cada empresa.
            $table->boolean('domestic_consumption')->default(false);
            $table->integer('limit')->nullable();
            $table->text('explanation')->nullable();
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
        Schema::dropIfExists('stock_movements');
    }
}
