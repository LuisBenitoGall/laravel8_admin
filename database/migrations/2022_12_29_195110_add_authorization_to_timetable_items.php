<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAuthorizationToTimetableItems extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('timetable_items', function (Blueprint $table){
            $table->boolean('with_authorization')->nullable()->after('required_warning');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('timetable_items', function (Blueprint $table){
            $table->dropColumn(['with_authorization']);
        });
    }
}
