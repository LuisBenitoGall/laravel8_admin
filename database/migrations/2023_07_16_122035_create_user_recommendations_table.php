<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRecommendationsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('user_recommendations', function (Blueprint $table) {
            $table->id();
            //FK:
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('song')->nullable();
            $table->integer('song_votes')->nullable();
            $table->text('phrase')->nullable();
            $table->integer('phrase_votes')->nullable();
            $table->text('image')->nullable();
            $table->integer('image_votes')->nullable();
            $table->text('scene')->nullable();
            $table->integer('scene_votes')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->dateTime('published_end')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('user_recommendations');
    }
}
