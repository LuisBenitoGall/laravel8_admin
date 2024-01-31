<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProjectRequiredToCompanySettings extends Migration{
    /**
     * Run the migrations.
     *
     * Establece si se debe generar obligatoriamente un proyecto junto con un pedido.
     *
     * @return void
     */
    public function up(){
        Schema::table('company_settings', function (Blueprint $table){
            $table->boolean('project_required')->default(false)->after('accounting_account_digits');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('company_settings', function (Blueprint $table){
            $table->dropColumn(['project_required']);
        });
    }
}
