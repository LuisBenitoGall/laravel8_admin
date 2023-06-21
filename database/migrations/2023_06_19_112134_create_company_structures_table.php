<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyStructuresTable extends Migration{
    /**
     * Run the migrations.
     *
     * Modelo para generar estructuras propias de cada empresa (obras, instalaciones, centros diversos,...)
     *
     * @return void
     */
    public function up(){
        Schema::create('company_structures', function (Blueprint $table) {
            $table->id();
            //FK:
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('name');
            $table->string('slug');
            $table->longText('description')->nullable();
            //FK:
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
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
        Schema::dropIfExists('company_structures');
    }
}
