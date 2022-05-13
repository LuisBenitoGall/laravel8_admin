<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskMessagesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('task_messages', function (Blueprint $table){
            $table->id();
            $table->text('message');
            //FK:
            $table->unsignedBigInteger('task_id')->nullable();
            $table->foreign('task_id')->references('id')->on('tasks');
            //FK:
            $table->unsignedBigInteger('from');
            $table->foreign('from')->references('id')->on('users');
            $table->text('to')->nullable();     // Destinatarios del mensaje.
            $table->boolean('taken')->default(false);   // Mensaje leído
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('task_messages');
    }
}
