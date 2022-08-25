<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidencesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('incidences', function (Blueprint $table){
            $table->id();
            $table->string('incidence_num')->nullable();
            $table->string('incidence_num_cardinal')->nullable();
            $table->integer('origin');      //1: incidencia interna     2: incidencia externa
            //FK:
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            //FK:
            $table->bigInteger('provider_id')->unsigned()->nullable();
            $table->foreign('provider_id')->references('id')->on('companies');
            //FK:
            $table->bigInteger('provider_manager_id')->unsigned()->nullable();
            $table->foreign('provider_manager_id')->references('id')->on('users');
            //FK:
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('companies');
            //FK:
            $table->bigInteger('customer_manager_id')->unsigned()->nullable();
            $table->foreign('customer_manager_id')->references('id')->on('users');
            $table->longText('observations')->nullable(); 
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('incidences');
    }
}
