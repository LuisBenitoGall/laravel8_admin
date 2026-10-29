<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdToUserCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_categories', function (Blueprint $table) {
            //FK:
            $table->unsignedBigInteger('category_id')->nullable()->after('user_id');
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_categories', function (Blueprint $table) {
            $table->dropColumn(['category_id']);
        });
    }
}
