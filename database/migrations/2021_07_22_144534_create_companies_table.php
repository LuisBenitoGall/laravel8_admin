<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('companies', function (Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('tradename')->nullable();
            $table->string('acronym')->nullable();
            $table->string('logo')->nullable();
            $table->string('nif')->nullable();
            $table->string('ccc')->nullable();
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
        Schema::dropIfExists('companies');
    }
}
