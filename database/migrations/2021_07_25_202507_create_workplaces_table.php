<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkplacesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('workplaces', function (Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('slug');
            //FK:
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->boolean('featured')->default(false);
            $table->string('logo')->nullable();
            $table->string('address')->nullable();
            $table->string('cp')->nullable();
            //FK:
            $table->unsignedBigInteger('town_id')->nullable();
            $table->foreign('town_id')->references('id')->on('towns');
            $table->string('nif')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('workplaces');
    }
}
