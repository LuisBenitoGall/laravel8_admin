<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToStockMovements extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('stock_movements', function (Blueprint $table){
            $table->boolean('status')->default(true)->after('explanation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('stock_movements', function (Blueprint $table){
            $table->dropColumn(['status']);
        });
    }
}
