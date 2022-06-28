<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPatternToProjects extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('projects', function (Blueprint $table){
            $table->string('project_num')->nullable()->after('id');
            $table->string('num_cardinal')->nullable()->after('project_num');
            //FK:
            $table->unsignedBigInteger('pattern_id')->nullable()->after('num_cardinal');
            $table->foreign('pattern_id')->references('id')->on('project_patterns');
            $table->string('project_old_num')->nullable()->after('pattern_id');
            $table->boolean('restricted_access')->default(false)->after('team');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('projects', function (Blueprint $table){
            $table->dropColumn(['project_num', 'num_cardinal', 'pattern_id']);
        });
    }
}
