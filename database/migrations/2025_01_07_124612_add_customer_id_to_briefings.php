<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerIdToBriefings extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('briefings', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('customer_id')->nullable()->after('company_id');
            $table->foreign('customer_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('briefings', function (Blueprint $table){
            $table->dropColumn(['customer_id']);
        });
    }
}
