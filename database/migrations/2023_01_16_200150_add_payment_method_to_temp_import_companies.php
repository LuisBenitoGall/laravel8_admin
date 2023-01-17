<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentMethodToTempImportCompanies extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('temp_import_companies', function (Blueprint $table){
            $table->string('payment_method')->nullable()->after('payday');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('temp_import_companies', function (Blueprint $table){
            $table->dropColumn(['payment_method']);
        });
    }
}
