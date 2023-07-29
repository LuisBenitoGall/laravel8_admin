<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizEditionQuestionsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('quiz_edition_questions', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('quiz_edition_id')->nullable();
            $table->foreign('quiz_edition_id')->references('id')->on('quiz_editions');
            //FK:
            $table->unsignedBigInteger('question_id')->nullable();
            $table->foreign('question_id')->references('id')->on('questions');
            $table->integer('sort')->nullable();
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
        Schema::dropIfExists('quiz_edition_questions');
    }
}
