<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSwiftToBanks extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('banks', function (Blueprint $table){
            $table->char('swift', 11)->nullable()->after('tradename');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('banks', function (Blueprint $table){
            $table->dropColumn(['swift']);
        });
    }
}
