<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBalanceToSeatLines extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('seat_lines', function (Blueprint $table) {
            $table->float('balance',12,2)->nullable()->after('credit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('seat_lines', function (Blueprint $table) {
            $table->dropColumn(['balance']);
        });
    }
}
