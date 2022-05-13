<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToRoles extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('roles', function (Blueprint $table){
            $table->text('description')->nullable()->after('guard_name');
            //FK:
            $table->unsignedBigInteger('company_id')->nullable()->after('description');
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('roles', function (Blueprint $table){
            $table->dropColumn(['description', 'company_id']);
        });
    }
}
