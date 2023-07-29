<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumToQuizEditions extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('quiz_editions', function (Blueprint $table){
            $table->integer('num_questions')->nullable()->after('time');
            $table->integer('num_days')->nullable()->after('num_questions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('quiz_editions', function (Blueprint $table){
            $table->dropColumn(['num_questions', 'num_days']);
        });
    }
}
