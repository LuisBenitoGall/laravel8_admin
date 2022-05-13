<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('questions', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('name');
            $table->string('slug');
            $table->longText('question')->nullable();
            $table->text('videos')->nullable();
            $table->integer('type')->nullable();
            $table->integer('answer_type')->nullable();
            $table->longText('keywords')->nullable();
            $table->integer('keywords_min')->default(0);
            $table->integer('time')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('questions');
    }
}
