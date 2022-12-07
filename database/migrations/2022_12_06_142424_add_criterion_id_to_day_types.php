<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCriterionIdToDayTypes extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('day_types', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('criterion_id')->nullable()->after('modifiable');
            $table->foreign('criterion_id')->references('id')->on('day_types_criterions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('day_types', function (Blueprint $table){
            $table->dropColumn(['criterion_id']);
        });
    }
}
