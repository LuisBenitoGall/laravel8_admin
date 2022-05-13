<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizEditionsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('quiz_editions', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('quiz_id')->nullable();
            $table->foreign('quiz_id')->references('id')->on('quizzes');
            $table->string('name');
            $table->string('slug');
            $table->text('description');
            $table->string('logo')->nullable();
            $table->dateTime('start')->nullable();
            $table->dateTime('finish')->nullable();
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
        Schema::dropIfExists('quiz_editions');
    }
}
