<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizEditionTeamsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('quiz_edition_teams', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('quiz_edition_id')->nullable();
            $table->foreign('quiz_edition_id')->references('id')->on('quiz_editions');
            //FK:
            $table->unsignedBigInteger('team_id')->nullable();
            $table->foreign('team_id')->references('id')->on('teams');
            $table->integer('points')->default(0);
            $table->integer('time')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('quiz_edition_teams');
    }
}
