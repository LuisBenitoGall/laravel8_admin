<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMainChartToProjects extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('projects', function (Blueprint $table){
            $table->char('chart', 2)->nullable()->after('groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('projects', function (Blueprint $table){
            $table->dropColumn(['chart']);
        });
    }
}
