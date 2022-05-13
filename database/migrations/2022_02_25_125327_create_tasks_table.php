<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('tasks', function (Blueprint $table){
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            //FK:
            $table->unsignedBigInteger('project_id')->nullable();
            $table->foreign('project_id')->references('id')->on('projects');
            //FK:
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->foreign('assigned_to')->references('id')->on('users');
            $table->date('delivery_date')->nullable();
            $table->integer('estimated_hours')->nullable();     //Se guardará su valor en minutos.
            $table->text('partial_hours')->nullable();
            $table->integer('hours')->nullable();               //Se guardará su valor en minutos.
            //FK:
            $table->unsignedBigInteger('status')->nullable();
            $table->foreign('status')->references('id')->on('task_status');
            $table->integer('column_order')->nullable();
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
        Schema::dropIfExists('tasks');
    }
}
