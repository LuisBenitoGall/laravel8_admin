<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDayTypesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('day_types', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('name')->nullable();
            $table->text('color')->nullable();
            $table->boolean('is_working')->default(0);  //Si la jornada, para la empresa, es laborable.
            $table->boolean('working_day')->default(0);     //Si es jornada de trabajo pero con características especiales (ej.: teletrabajo)
            $table->boolean('user_enabled')->default(0);
            $table->boolean('cumulative')->default(0);  //Determina si el tipo de día se acumula de un año para el otro.
            $table->boolean('rank')->default(0);    //Si se pueden establecer rangos de selección con este tipo de jornada.
            $table->boolean('modifiable')->default(0); //Si es modificable.
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
        Schema::dropIfExists('day_types');
    }
}
