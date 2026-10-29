<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillablePercentageToProducts extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('products', function (Blueprint $table) {
            $table->float('billable_percentage', 4, 2)->default(config('constants.BILLABLE_PERCENTAGE_DEFAULT_'))->after('iva');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['billable_percentage']);
        });
    }
}
