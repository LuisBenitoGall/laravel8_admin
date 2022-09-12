<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstimatedReceptionDateToDeliveries extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('deliveries', function (Blueprint $table){
            $table->date('estimated_reception_date')->nullable()->after('send_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('deliveries', function (Blueprint $table){
            $table->dropColumn(['estimated_reception_date']);
        });
    }
}
