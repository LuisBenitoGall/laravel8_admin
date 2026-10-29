<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatEffectsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('seat_effects', function (Blueprint $table){
            $table->id();
            //FK:
            $table->bigInteger('seat_id')->unsigned()->nullable();
            $table->foreign('seat_id')->references('id')->on('seats');
            //FK:
            $table->bigInteger('effect_id')->unsigned()->nullable();
            $table->foreign('effect_id')->references('id')->on('effects');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('seat_effects');
    }
}
