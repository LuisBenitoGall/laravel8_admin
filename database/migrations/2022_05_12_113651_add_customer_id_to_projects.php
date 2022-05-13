<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerIdToProjects extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('projects', function (Blueprint $table){
            //FK:
            $table->unsignedBigInteger('customer_id')->nullable()->after('team');
            $table->foreign('customer_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('projects', function (Blueprint $table){
            $table->dropColumn(['customer_id']);
        });
    }
}
