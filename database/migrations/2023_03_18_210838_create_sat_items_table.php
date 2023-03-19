<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSatItemsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('sat_items', function (Blueprint $table) {
            $table->id();
            //FK:
            $table->unsignedBigInteger('sat_id')->nullable();
            $table->foreign('sat_id')->references('id')->on('sats');
            $table->longText('observations')->nullable();
            $table->string('doc')->nullable();
            //FK:
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('sat_items');
    }
}
