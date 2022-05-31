<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProceduresTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('procedures', function (Blueprint $table){
            $table->id();
            $table->string('procedure_num')->nullable();
            $table->string('num_cardinal')->nullable();
            //FK:
            $table->unsignedBigInteger('pattern_id')->nullable();
            $table->foreign('pattern_id')->references('id')->on('procedure_patterns');
            $table->string('procedure_old_num')->nullable();
            $table->string('name');
            $table->string('slug');
            //FK:
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->text('description')->nullable();
            //FK:
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('updated_by');
            $table->foreign('updated_by')->references('id')->on('users');
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
        Schema::dropIfExists('procedures');
    }
}
