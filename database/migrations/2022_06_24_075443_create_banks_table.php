<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBanksTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('banks', function (Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('tradename')->nullable();
            $table->string('lei')->nullable();
            $table->string('eu_code')->nullable();
            $table->string('supervisor_code')->nullable();
            $table->boolean('status')->default(false);
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
        Schema::dropIfExists('banks');
    }
}
