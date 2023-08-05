<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRecommendationVotesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('user_recommendation_votes', function (Blueprint $table) {
            $table->id();
            //FK:
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('recommendation_id')->nullable();
            $table->foreign('recommendation_id')->references('id')->on('user_recommendations');
            $table->integer('song')->nullable();
            $table->integer('phrase')->nullable();
            $table->integer('scene')->nullable();
            $table->integer('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('user_recommendation_votes');
    }
}
