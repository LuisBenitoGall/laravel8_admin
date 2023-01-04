<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnnualRepetitionToDayTypes extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('day_types', function (Blueprint $table){
            $table->boolean('annual_repetition')->default(false)->after('modifiable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('day_types', function (Blueprint $table){
            $table->dropColumn(['annual_repetition']);
        });
    }
}
