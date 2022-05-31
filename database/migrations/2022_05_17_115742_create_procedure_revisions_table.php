<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcedureRevisionsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('procedure_revisions', function (Blueprint $table){
            $table->id();
            $table->integer('order')->default(0);
            //FK:
            $table->unsignedBigInteger('procedure_id')->nullable();
            $table->foreign('procedure_id')->references('id')->on('procedures');
            $table->longText('content')->nullable();
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
        Schema::dropIfExists('procedure_revisions');
    }
}
