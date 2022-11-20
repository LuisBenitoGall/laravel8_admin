<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToQuestions extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('questions', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('quiz_id')->after('company_id');
            $table->foreign('quiz_id')->references('id')->on('quizzes');
            $table->text('url')->nullable()->after('videos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('questions', function (Blueprint $table){
            $table->dropColumn(['quiz_id', 'url']);
        });
    }
}
