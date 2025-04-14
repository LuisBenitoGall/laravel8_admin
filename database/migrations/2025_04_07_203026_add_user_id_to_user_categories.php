<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToUserCategories extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('user_categories', function (Blueprint $table) {
            //FK:
            $table->unsignedBigInteger('user_id')->nullable()->after('slug');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('user_categories', function (Blueprint $table) {
            $table->dropColumn(['user_id']);
        });
    }
}
