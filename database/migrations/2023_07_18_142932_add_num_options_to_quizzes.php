<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumOptionsToQuizzes extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('quizzes', function (Blueprint $table){
            $table->integer('num_options')->default(3)->after('managers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('quizzes', function (Blueprint $table){
            $table->dropColumn(['num_options']);
        });
    }
}
