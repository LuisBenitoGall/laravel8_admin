<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResponseToIncidenceMessages extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('incidence_messages', function (Blueprint $table){
            $table->bigInteger('response_to')->nullable()->after('id');
            $table->bigInteger('thread')->nullable()->after('response_to');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('incidence_messages', function (Blueprint $table){
            $table->dropColumn(['response_to', 'thread']);
        });
    }
}
