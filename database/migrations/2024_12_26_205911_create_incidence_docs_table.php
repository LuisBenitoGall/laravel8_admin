<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidenceDocsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('incidence_docs', function (Blueprint $table){
            $table->id();
            $table->string('doc');
            //FK:
            $table->unsignedBigInteger('incidence_id')->nullable();
            $table->foreign('incidence_id')->references('id')->on('incidences');
            //FK:
            $table->unsignedBigInteger('incidence_message_id')->nullable();
            $table->foreign('incidence_message_id')->references('id')->on('incidence_messages');
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('incidence_docs');
    }
}
