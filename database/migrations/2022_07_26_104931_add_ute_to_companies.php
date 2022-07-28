<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUteToCompanies extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('companies', function (Blueprint $table){
            $table->boolean('is_ute')->default(false)->after('ccc');    //Indica si la empresa es UTE (Unión Temporal de Empresas)
            $table->dateTime('ute_start_at')->nullable()->after('is_ute');
            $table->dateTime('ute_finish_at')->nullable()->after('ute_start_at');
        });     
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('companies', function (Blueprint $table){
            $table->dropColumn(['is_ute', 'ute_start_at', 'ute_finish_at']);
        });
    }
}
