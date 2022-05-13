<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimetablesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('timetables', function (Blueprint $table){
            $table->id();
            $table->date('date');
            $table->time('hour');
            //FK:
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('timetable_items');
            $table->integer('flag'); //0 = pendiente; 1 = entrada; 2 = salida
            $table->integer('revision')->default(0);
            $table->text('comments')->nullable();
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
        Schema::dropIfExists('timetables');
    }
}
