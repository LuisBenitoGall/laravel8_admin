<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidenceMessagesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('incidence_messages', function (Blueprint $table) {
            $table->id();
            //FK:
            $table->bigInteger('incidence_id')->unsigned()->nullable();
            $table->foreign('incidence_id')->references('id')->on('incidences');
            //FK:
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->longText('message')->nullable();
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
    public function down(){
        Schema::dropIfExists('incidence_messages');
    }
}
