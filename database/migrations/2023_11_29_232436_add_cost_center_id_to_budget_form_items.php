<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCostCenterIdToBudgetFormItems extends Migration{
    /**
     * Run the migrations.
     *
     * Para obtener la venta por centros de coste según el producto.
     *
     * @return void
     */
    public function up(){
        Schema::table('budget_form_items', function (Blueprint $table) {
            //FK:
            $table->unsignedBigInteger('cost_center_id')->nullable()->after('sort_block');
            $table->foreign('cost_center_id')->references('id')->on('cost_centers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('budget_form_items', function (Blueprint $table) {
            $table->dropColumn(['cost_center_id']);
        });
    }
}
