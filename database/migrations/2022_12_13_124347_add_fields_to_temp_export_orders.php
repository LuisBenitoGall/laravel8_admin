<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToTempExportOrders extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('temp_export_orders', function (Blueprint $table){
            $table->string('estimated_date')->nullable()->after('order_date');
            $table->string('delivery_date')->nullable()->after('estimated_date');
            $table->string('order_title')->nullable()->after('order_old');
            $table->string('order_status')->nullable()->after('order_title');
            $table->float('order_discount',5,2)->default(0)->after('order_qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('temp_export_orders', function (Blueprint $table){
            $table->dropColumn(['estimated_date', 'delivery_date', 'order_title', 'order_status', 'order_discount']);
        });
    }
}
