<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatRoomUsersTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('chat_room_users', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('chat_room_id');
            $table->foreign('chat_room_id')->references('id')->on('chat_rooms');
            $table->integer('role')->default(1);
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('chat_room_users');
    }
}
