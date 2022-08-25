<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimetableItemsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('timetable_items', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->boolean('basic_item')->default(false);  //Indica si el concepto es básico o no.
            $table->integer('type');  //Indica si la item es entrada (1) o salida (2)
            $table->bigInteger('own_output')->nullable();       //Indica si el movimiento de entrada tiene movimiento de salida exclusivo.
            $table->integer('fixed')->nullable();    //minutos fijos de un item.
            $table->string('icon')->nullable();
            $table->integer('min')->nullable();      //mínimo de minutos
            $table->integer('max')->nullable();      //máximo de minutos
            $table->integer('qty')->nullable();      //nº veces x día
            $table->time('min_hour')->nullable();
            $table->string('color')->nullable();
            $table->boolean('reviewable')->nullable();
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
        Schema::dropIfExists('timetable_items');
    }
}
