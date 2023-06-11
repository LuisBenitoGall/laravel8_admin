<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProtocolItemsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('protocol_items', function (Blueprint $table) {
            $table->id();
            //FK:
            $table->unsignedBigInteger('protocol_id')->nullable();
            $table->foreign('protocol_id')->references('id')->on('protocols');
            $table->string('title');
            $table->string('slug');
            $table->longText('description')->nullable();
            $table->integer('sort')->nullable();
            //FK:
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
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
        Schema::dropIfExists('protocol_items');
    }
}
