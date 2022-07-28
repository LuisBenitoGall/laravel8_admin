<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMotherCoToCompanies extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('companies', function (Blueprint $table){
            $table->bigInteger('mother_co')->nullable()->after('id');
            $table->string('mother_co_num')->nullable()->after('mother_co');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('companies', function (Blueprint $table){
            $table->dropColumn(['mother_co', 'mother_co_num']);
        });
    }
}
