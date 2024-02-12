<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProviderWorkplaceIdToCustomerProviders extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('customer_providers', function (Blueprint $table) {
            //FK:
            $table->unsignedBigInteger('provider_workplace_id')->nullable()->after('customer_workplace_id');
            $table->foreign('provider_workplace_id')->references('id')->on('workplaces');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('customer_providers', function (Blueprint $table) {
            $table->dropColumn(['provider_workplace_id']);
        });
    }
}
