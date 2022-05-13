<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('contents', function (Blueprint $table){
            $table->id();
            $table->string('name')->nullable();     //Nombre del contenido
            $table->string('code')->unique();       //Código único del contenido
            $table->string('referrer')->nullable(); //Indica otro contenido que tome como referencia.
            $table->integer('type');                //1=info; 2=contenido.
            $table->text('title')->nullable();
            $table->text('slug')->nullable();
            $table->text('excerpt')->nullable();
            $table->text('content')->nullable();
            $table->text('tags')->nullable();
            $table->text('links')->nullable();
            $table->text('video')->nullable();
            $table->text('classes')->nullable();
            //FK:
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('status')->default(1);
            $table->text('observations')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->dateTime('published_end')->nullable();
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
        Schema::dropIfExists('contents');
    }
}
