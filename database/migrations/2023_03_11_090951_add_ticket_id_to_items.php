<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicketIdToItems extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('items', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('ticket_id')->nullable()->after('order_id');
            $table->foreign('ticket_id')->references('id')->on('tickets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('items', function (Blueprint $table){
            $table->dropColumn(['ticket_id']);
        });
    }
}
