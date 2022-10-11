<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizCompaniesTable extends Migration{
    /**
     * Run the migrations.
     *
     * Empresas que participan en cada edición de un quiz.
     *
     * @return void
     */
    public function up(){
        Schema::create('quiz_companies', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
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
        Schema::dropIfExists('quiz_companies');
    }
}
