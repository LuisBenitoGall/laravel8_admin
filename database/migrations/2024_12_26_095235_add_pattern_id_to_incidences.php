<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPatternIdToIncidences extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('incidences', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('pattern_id')->nullable()->after('incidence_num_cardinal');
            $table->foreign('pattern_id')->references('id')->on('incidence_patterns');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('incidences', function (Blueprint $table){
            $table->dropColumn(['pattern_id']);
        });
    }
}
