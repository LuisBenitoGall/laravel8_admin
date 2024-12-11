<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIncidenceOldNumToIncidences extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('incidences', function (Blueprint $table){
            $table->string('incidence_old_num')->nullable()->after('incidence_num_cardinal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('incidences', function (Blueprint $table){
            $table->dropColumn(['incidence_old_num']);   
        });
    }
}
