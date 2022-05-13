<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('employees', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('contract_id');
            $table->foreign('contract_id')->references('id')->on('contracts');
            //FK:
            $table->unsignedBigInteger('workplace_id');
            $table->foreign('workplace_id')->references('id')->on('workplaces');
            $table->string('job_title')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->float('weekly_hours', 4, 2)->default(0);
            $table->integer('time_control')->default(1);        //Indica si el empleado está sometido a control horario o no.
            $table->float('year_salary', 9, 2)->nullable()->default(0);
            $table->integer('status')->default(1);
            $table->text('observations')->nullable();
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
        Schema::dropIfExists('employees');
    }
}
