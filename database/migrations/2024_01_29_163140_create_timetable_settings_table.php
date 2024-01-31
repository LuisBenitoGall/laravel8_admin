<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimetableSettingsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('timetable_settings', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->integer('minute_discount')->nullable();     //Minutos a restar a los horarios de los empleados para jornadas donde no se contabiliza el tiempo de comida u otros conceptos.
            $table->date('md_from')->nullable();    //Fecha inicio de los minutos de descuento.
            $table->date('md_to')->nullable();      //Fecha fin de los minutos de descuento.
            $table->longText('timetable_doc_message')->nullable();
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
        Schema::dropIfExists('timetable_settings');
    }
}
