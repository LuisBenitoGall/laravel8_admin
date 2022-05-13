<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkCalendarsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('work_calendars', function (Blueprint $table) {
            $table->id();
            //FK:
            $table->unsignedBigInteger('workplace_id');
            $table->foreign('workplace_id')->references('id')->on('workplaces');
            //FK:
            $table->unsignedBigInteger('day_type_id')->nullable();
            $table->foreign('day_type_id')->references('id')->on('day_types');
            $table->date('date');
            $table->time('hours')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('work_calendars');
    }
}
