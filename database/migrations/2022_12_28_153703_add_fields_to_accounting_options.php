<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToAccountingOptions extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('accounting_options', function (Blueprint $table){
            $table->char('type', 1)->nullable()->after('company_id');       //Leyenda: s = venta; p = compra.
            $table->char('year', 4)->nullable()->after('type');
            $table->char('month', 2)->nullable()->after('year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('accounting_options', function (Blueprint $table){
            $table->dropColumn(['type', 'year', 'month']);
        });
    }
}
