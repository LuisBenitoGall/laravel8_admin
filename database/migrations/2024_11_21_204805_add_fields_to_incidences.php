<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToIncidences extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('incidences', function (Blueprint $table){
            $table->date('incidence_date')->nullable()->after('origin');
            $table->string('concept')->nullable()->after('customer_manager_id');
            //FK:
            $table->unsignedBigInteger('subject_id')->nullable()->after('provider_manager_id');
            $table->foreign('subject_id')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('created_by')->nullable()->after('status');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('incidences', function (Blueprint $table){
            $table->dropColumn(['incidence_date', 'concept', 'subject_id', 'created_by']);    
        });
    }
}
