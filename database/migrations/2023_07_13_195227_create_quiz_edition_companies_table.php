<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizEditionCompaniesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('quiz_edition_companies', function (Blueprint $table) {
            $table->id();
            //FK:
            $table->unsignedBigInteger('quiz_edition_id')->nullable();
            $table->foreign('quiz_edition_id')->references('id')->on('quiz_editions');
            //FK:
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->integer('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('quiz_edition_companies');
    }
}
