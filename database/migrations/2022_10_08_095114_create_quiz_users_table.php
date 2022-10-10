<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizUsersTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('quiz_users', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('quiz_edition_id')->nullable();
            $table->foreign('quiz_edition_id')->references('id')->on('quiz_editions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('quiz_users');
    }
}
