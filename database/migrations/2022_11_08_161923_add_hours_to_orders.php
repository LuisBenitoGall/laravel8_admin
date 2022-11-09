<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHoursToOrders extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('orders', function (Blueprint $table){
            $table->float('hours', 5, 2)->nullable()->after('bag_amount');
            $table->float('hours_extra', 5, 2)->nullable()->after('hours');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('orders', function (Blueprint $table){
            $table->dropColumn(['hours', 'hours_extra']);
        });
    }
}
