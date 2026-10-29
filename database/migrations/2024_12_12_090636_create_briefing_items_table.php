<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBriefingItemsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('briefing_items', function (Blueprint $table){
            $table->id();
            $table->string('concept');
            $table->longText('observations')->nullable();
            //FK:
            $table->unsignedBigInteger('briefing_id');
            $table->foreign('briefing_id')->references('id')->on('briefings');
            //FK:
            $table->unsignedBigInteger('template_item_id')->nullable();
            $table->foreign('template_item_id')->references('id')->on('briefing_template_items');
            $table->float('time_qty',11,2)->nullable();         //Cantidad de tiempo empleado
            $table->integer('time_unit')->nullable();           //Unidad de tiempo en que medir la tarea
            $table->dateTime('tested_at')->nullable();          //Fecha del test
            //FK:
            $table->unsignedBigInteger('tested_by')->nullable();    //Autor del test
            $table->foreign('tested_by')->references('id')->on('users');
            $table->integer('status')->default(1);
            //FK:
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('updated_by');
            $table->foreign('updated_by')->references('id')->on('users');
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
        Schema::dropIfExists('briefing_items');
    }
}
