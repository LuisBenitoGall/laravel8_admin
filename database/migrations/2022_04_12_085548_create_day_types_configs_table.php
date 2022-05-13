<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDayTypesConfigsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('day_types_configs', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('day_type_id')->nullable();
            $table->foreign('day_type_id')->references('id')->on('day_types');

            $table->year('year');
            $table->integer('min')->nullable();
            $table->integer('max')->nullable();
            $table->integer('max_per_month')->nullable();
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
        Schema::dropIfExists('day_types_configs');
    }
}
