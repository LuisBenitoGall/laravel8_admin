<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkUserCalendarsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('work_user_calendars', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('day_type_id')->nullable();
            $table->foreign('day_type_id')->references('id')->on('day_types');
            $table->date('date');
            $table->time('self_hours')->nullable();
            $table->timestamp('notified')->nullable();
            $table->string('doc')->nullable();
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('work_user_calendars');
    }
}
