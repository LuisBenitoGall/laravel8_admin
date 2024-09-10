<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductionPrevisionsToCompanySettings extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('company_settings', function (Blueprint $table){
            $table->boolean('production_previsions')->default(false)->after('project_required');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('company_settings', function (Blueprint $table){
            $table->dropColumn(['production_previsions']);
        });
    }
}
