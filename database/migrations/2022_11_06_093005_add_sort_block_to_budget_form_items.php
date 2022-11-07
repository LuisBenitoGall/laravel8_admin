<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSortBlockToBudgetFormItems extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('budget_form_items', function (Blueprint $table){
            $table->integer('sort_block')->nullable()->after('fixed_in_budget');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('budget_form_items', function (Blueprint $table){
            $table->dropColumn(['sort_block']);        
        });
    }
}
