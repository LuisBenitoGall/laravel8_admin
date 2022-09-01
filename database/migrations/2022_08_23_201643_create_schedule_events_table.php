<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleEventsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('schedule_events', function (Blueprint $table){
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            //FK:
            $table->bigInteger('schedule_id')->unsigned()->nullable();
            $table->foreign('schedule_id')->references('id')->on('schedules');
            //FK:
            $table->bigInteger('author')->unsigned()->nullable();
            $table->foreign('author')->references('id')->on('users');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->boolean('allDay')->default(false);
            $table->text('responsible')->nullable();
            $table->text('guests')->nullable();
            $table->text('products')->nullable();
            $table->text('place')->nullable();
            $table->integer('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('schedule_events');
    }
}
