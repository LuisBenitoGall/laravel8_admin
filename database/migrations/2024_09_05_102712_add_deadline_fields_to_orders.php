<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeadlineFieldsToOrders extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('orders', function (Blueprint $table){
            $table->integer('num_deadlines')->default(1)->after('hours_extra');
            $table->integer('days_between_deadlines')->nullable()->after('num_deadlines');
            //FK:
            $table->bigInteger('accounting_sale_id')->unsigned()->nullable()->after('days_between_deadlines');
            $table->foreign('accounting_sale_id')->references('id')->on('accounting_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('orders', function (Blueprint $table){
            $table->dropColumn(['num_deadlines', 'days_between_deadlines', 'accounting_sale_id']);
        });
    }
}
