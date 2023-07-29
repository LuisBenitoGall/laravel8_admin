<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizEditionAnswersTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('quiz_edition_answers', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('quiz_edition_question_id')->nullable();
            $table->foreign('quiz_edition_question_id')->references('id')->on('quiz_edition_questions');
            $table->integer('answer')->nullable();
            //FK:
            $table->unsignedBigInteger('team_id')->nullable();
            $table->foreign('team_id')->references('id')->on('teams');
            $table->string('answer_value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('quiz_edition_answers');
    }
}
