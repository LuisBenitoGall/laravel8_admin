<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToQuizEditionQuestions extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('quiz_edition_questions', function (Blueprint $table) {
            $table->dateTime('date_from')->nullable();
            $table->dateTime('date_to')->nullable()->after('date_from');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('quiz_edition_questions', function (Blueprint $table) {
            $table->dropColumn(['date_from', 'date_to']);
        });
    }
}
