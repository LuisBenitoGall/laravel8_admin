<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffPickObservationsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('staff_pick_observations', function (Blueprint $table) {
            $table->id();
            //FK:
            $table->unsignedBigInteger('candidate_id');
            $table->foreign('candidate_id')->references('id')->on('staff_pick_candidates');
            $table->date('meeting_date')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('staff_pick_observations');
    }
}
