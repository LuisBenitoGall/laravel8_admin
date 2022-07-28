<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParticipationToCompanyUtes extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('company_utes', function (Blueprint $table){
            $table->float('participation_percent', 5, 2)->nullable()->after('member_id');    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('company_utes', function (Blueprint $table){
            $table->dropColumn(['participation_percent']);
        });
    }
}
