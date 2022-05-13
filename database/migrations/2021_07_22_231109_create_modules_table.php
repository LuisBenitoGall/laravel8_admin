<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('modules', function (Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('label');
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->integer('level')->nullable();   // (*)
            $table->text('translations')->nullable();
            $table->text('explanation')->nullable();
            $table->integer('status')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * (*) Level determina los siguientes casos:
     * 1: Módulos exclusivos del Administrador, sin acceso de los usuarios.
     * 2: Módulos básicos, presentes en cualquier instalación.
     * 3: Módulos opcionales.
     */

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('modules');
    }
}
