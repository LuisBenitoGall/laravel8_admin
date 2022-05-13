<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('projects', function (Blueprint $table){
            $table->id();
            $table->string('code')->nullable();
            $table->string('contest_code')->nullable();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->text('briefing')->nullable();
            //FK:
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            //FK:
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('owner')->nullable();
            $table->foreign('owner')->references('id')->on('users');
            $table->text('team')->nullable();
            $table->date('estimated_delivery_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->float('hours_forecast',6,2)->nullable();
            $table->float('hours_worked',6,2)->nullable();
            $table->float('task_hours',6,2)->nullable();
            $table->float('completed',5,2)->default(0);
            $table->text('groups')->nullable();
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
        Schema::dropIfExists('projects');
    }
}
