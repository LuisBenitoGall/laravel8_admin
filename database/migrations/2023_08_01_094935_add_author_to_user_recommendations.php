<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAuthorToUserRecommendations extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('user_recommendations', function (Blueprint $table){
            $table->text('author')->nullable()->after('phrase_votes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('user_recommendations', function (Blueprint $table){
            $table->dropColumn(['author']);
        });
    }
}
