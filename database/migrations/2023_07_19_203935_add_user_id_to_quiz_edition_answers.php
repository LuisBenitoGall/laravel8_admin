<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToQuizEditionAnswers extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('quiz_edition_answers', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('quiz_edition_id')->nullable()->after('id');
            $table->foreign('quiz_edition_id')->references('id')->on('quiz_editions');
            //FK:
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('quiz_edition_answers', function (Blueprint $table){
            $table->dropColumn(['quiz_edition_id', 'user_id']);
        });
    }
}
