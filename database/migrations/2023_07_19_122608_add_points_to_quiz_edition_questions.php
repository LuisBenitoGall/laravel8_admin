<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPointsToQuizEditionQuestions extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('quiz_edition_questions', function (Blueprint $table) {
            $table->integer('points')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('quiz_edition_questions', function (Blueprint $table) {
            $table->dropColumn(['points']);
        });
    }
}
