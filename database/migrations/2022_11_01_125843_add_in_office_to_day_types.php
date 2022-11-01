<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInOfficeToDayTypes extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('day_types', function (Blueprint $table){
            $table->boolean('in_office')->default(false)->after('is_working');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('day_types', function (Blueprint $table){
            $table->dropColumn(['in_office']);
        });
    }
}
