<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToQuizzes extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('quizzes', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('owner')->nullable()->after('description');
            $table->foreign('owner')->references('id')->on('users');
            $table->string('managers')->nullable()->after('owner');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('quizzes', function (Blueprint $table){
            $table->dropColumn(['owner', 'managers']);
        });
    }
}
