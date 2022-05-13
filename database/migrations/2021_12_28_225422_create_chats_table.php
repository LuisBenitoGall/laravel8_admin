<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('chats', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('sender');
            $table->foreign('sender')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('receiver')->nullable();
            $table->foreign('receiver')->references('id')->on('users');
            $table->timestamp('read')->nullable();
            $table->text('message');
            $table->text('attached')->nullable();
            //FK:
            $table->unsignedBigInteger('chat_room_id')->nullable();
            $table->foreign('chat_room_id')->references('id')->on('chat_rooms');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('chats');
    }
}
