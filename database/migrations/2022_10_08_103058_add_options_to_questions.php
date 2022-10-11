<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOptionsToQuestions extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('questions', function (Blueprint $table){
            $table->longText('options')->nullable()->after('question');
            $table->longText('right_option')->nullable()->after('options');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('questions', function (Blueprint $table){
            $table->dropColumn(['options', 'right_option']);
        });
    }
}
