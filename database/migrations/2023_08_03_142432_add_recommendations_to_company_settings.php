<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecommendationsToCompanySettings extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('company_settings', function (Blueprint $table) {
            $table->boolean('recommendations')->default(false)->after('public_info');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn(['recommendations']);
        });
    }
}
